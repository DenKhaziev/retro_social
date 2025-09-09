<?php
function get_user_posts(int $userId): array {
    return db_query('
        SELECT p.*,
               COUNT(DISTINCT c.id) AS comments_count,
               0 AS likes_count
        FROM blog_posts p
        LEFT JOIN blog_comments c ON c.post_id = p.id
        WHERE p.user_id = ? AND p.is_draft = 0
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ', [$userId])->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_post(int $postId): ?array {
    $stmt = db_query('
        SELECT p.*,
               (SELECT COUNT(*) FROM blog_comments bc WHERE bc.post_id = p.id) AS comments_count,
               0 AS likes_count
        FROM blog_posts p
        WHERE p.id = ?
        LIMIT 1
    ', [$postId]);
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}
function inc_post_view(int $postId, int $currentUserId): void {
    // достанем владельца поста
    $stmt = db_query('SELECT user_id FROM blog_posts WHERE id=? LIMIT 1', [$postId]);
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        return;
    }

    // если зашёл не автор — увеличиваем просмотры
    if ((int)$row['user_id'] !== (int)$currentUserId) {
        db_query('UPDATE blog_posts SET views_count = views_count + 1 WHERE id=?', [$postId]);
    }
}

function get_post_comments(int $postId): array {
    return db_query('
        SELECT c.*, u.login, u.avatar_path
        FROM blog_comments c
        JOIN users u ON u.id = c.user_id
        WHERE c.post_id=?
        ORDER BY c.created_at ASC, c.id ASC
    ', [$postId])->get_result()->fetch_all(MYSQLI_ASSOC);
}

function add_post_comment(int $postId, int $userId, string $body): bool {
    $body = trim($body);
    if ($body === '') return false;
    db_query('INSERT INTO blog_comments (post_id,user_id,body,created_at) VALUES (?,?,?,NOW())',
        [$postId, $userId, $body]);
    return true;
}


