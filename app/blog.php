<?php
function get_user_posts(int $userId): array {
    return db_query('
        SELECT p.*,
               COUNT(DISTINCT c.id) AS comments_count,
               COUNT(DISTINCT l.user_id) AS likes_count
        FROM blog_posts p
        LEFT JOIN blog_comments c ON c.post_id = p.id
        LEFT JOIN blog_likes    l ON l.post_id = p.id
        WHERE p.user_id = ? AND p.is_draft = 0
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ', [$userId])->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_post(int $postId): ?array {
    $row = db_query('SELECT * FROM blog_posts WHERE id=? LIMIT 1', [$postId])->get_result()->fetch_assoc();
    return $row ?: null;
}
function inc_post_view(int $postId): void {
    db_query('UPDATE blog_posts SET views_count = views_count + 1 WHERE id=?', [$postId]);
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
