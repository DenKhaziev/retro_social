<?php
function get_messages_between(int $user1, int $user2): array {
    $stmt = db_query("
        SELECT *
        FROM messages
        WHERE (sender_id = ? AND receiver_id = ?)
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY created_at ASC
    ", [$user1, $user2, $user2, $user1]);

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function mark_messages_as_read(int $myId, int $fromUserId): void {
    db_query("
        UPDATE messages
        SET read_at = NOW()
        WHERE receiver_id = ? AND sender_id = ? AND read_at IS NULL
    ", [$myId, $fromUserId]);
}

function get_user_dialogs(int $uid): array
{
    $sql = "
        SELECT
            CASE
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END AS other_user_id,
            MAX(id) AS last_message_id
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        GROUP BY other_user_id
        ORDER BY MAX(created_at) DESC
    ";

    // 1. Получаем список other_user_id и last_message_id
    $stmt = db_query($sql, [$uid, $uid, $uid]);
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC); // <--- ВАЖНО: дочитываем сразу всё

    $dialogs = [];

    // 2. Теперь можно итерироваться
    foreach ($rows as $row) {
        $otherUserId = (int)$row['other_user_id'];

        // Получаем последнее сообщение между пользователями
        $msgStmt = db_query("
            SELECT *
            FROM messages
            WHERE (sender_id = ? AND receiver_id = ?)
               OR (sender_id = ? AND receiver_id = ?)
            ORDER BY created_at DESC
            LIMIT 1
        ", [$uid, $otherUserId, $otherUserId, $uid]);

        $lastMessage = $msgStmt->get_result()->fetch_assoc();

        if ($lastMessage) {
            // Получаем имя собеседника
            $userStmt = db_query("SELECT login FROM users WHERE id = ?", [$otherUserId]);
            $user = $userStmt->get_result()->fetch_assoc();

            $dialogs[] = [
                'other_user_id'     => $otherUserId,
                'other_user_login'  => $user['login'] ?? 'Пользователь',
                'last_message'      => $lastMessage['body'],
                'last_created_at'   => $lastMessage['created_at'],
                'last_read'         => $lastMessage['read_at'],
                'last_receiver_id'  => $lastMessage['receiver_id'],
            ];
        }
    }

    return $dialogs;
}

