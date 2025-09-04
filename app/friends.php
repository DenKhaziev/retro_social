<?php

// Получает массив друзей для пользователя (accepted связи)
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

