<?php

function get_photo_comments(int $photoId): array
{
    $stmt = db_query("
        SELECT c.id, c.photo_id, c.user_id, c.body, c.created_at,
               u.login, u.avatar_path
        FROM photo_comments c
        JOIN users u ON u.id = c.user_id
        WHERE c.photo_id = ?
        ORDER BY c.created_at ASC, c.id ASC
    ", [$photoId]);

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function photo_add_comment(int $photoId, int $userId, string $body): array
{
    $photo = photo_find($photoId);
    if (!$photo) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    $body = trim($body);
    if ($body === '') {
        return ['ok' => false, 'error' => 'empty'];
    }

    db_query("
        INSERT INTO photo_comments (photo_id, user_id, body, created_at)
        VALUES (?, ?, ?, NOW())
    ", [$photoId, $userId, $body]);

    return ['ok' => true, 'error' => null];
}
