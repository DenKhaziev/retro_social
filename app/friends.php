<?php

function get_user_friends(int $userId): array
{
    $stmt = db_query("
        SELECT 
            CASE 
                WHEN f.user_id = ? THEN f.friend_id
                ELSE f.user_id
            END AS friend_id,
            u.login,
            u.avatar_path
        FROM friendships f
        JOIN users u ON u.id = CASE 
            WHEN f.user_id = ? THEN f.friend_id
            ELSE f.user_id
        END
        WHERE f.status = 'accepted' AND (f.user_id = ? OR f.friend_id = ?)
    ", [$userId, $userId, $userId, $userId]);

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_friendship_status(int $a, int $b): ?string
{
    $stmt = db_query("
        SELECT status FROM friendships 
        WHERE (user_id = ? AND friend_id = ?) 
           OR (user_id = ? AND friend_id = ?)
        LIMIT 1
    ", [$a, $b, $b, $a]);

    $row = $stmt->get_result()->fetch_assoc();
    return $row['status'] ?? null;
}

function get_pending_friend_requests(int $userId): array
{
    $stmt = db_query("
        SELECT f.user_id AS requester_id, u.login, u.avatar_path
        FROM friendships f
        JOIN users u ON u.id = f.user_id
        WHERE f.friend_id = ? AND f.status = 'pending'
    ", [$userId]);

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function are_friends(int $userId1, int $userId2): bool {
    if ($userId1 === $userId2) return true;
    $stmt = db_query("
        SELECT 1
        FROM friendships
        WHERE status = 'accepted'
          AND (
              (user_id = ? AND friend_id = ?)
              OR (user_id = ? AND friend_id = ?)
          )
        LIMIT 1
    ", [$userId1, $userId2, $userId2, $userId1]);
    return (bool)$stmt->get_result()->fetch_row();
}

