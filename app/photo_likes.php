<?php

function get_photo_owner(int $photoId): ?array
{
    $stmt = db_query("SELECT id, file_path, user_id FROM photos WHERE id = ? LIMIT 1", [$photoId]);
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function get_photo_likes(int $photoId): array
{
    $stmt = db_query("
        SELECT u.id, u.login, u.avatar_path
        FROM photo_likes pl
        JOIN users u ON u.id = pl.user_id
        WHERE pl.photo_id = ?
        ORDER BY pl.created_at DESC
    ", [$photoId]);

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

