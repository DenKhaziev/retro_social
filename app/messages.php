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
            d.other_user_id,
            u.login AS other_user_login,
            u.avatar_path AS other_user_avatar,
            m.body   AS last_message,
            m.created_at AS last_created_at,
            m.read_at    AS last_read,
            m.receiver_id AS last_receiver_id
        FROM (
            SELECT
                CASE
                    WHEN sender_id = ? THEN receiver_id
                    ELSE sender_id
                END AS other_user_id,
                MAX(id) AS last_message_id
            FROM messages
            WHERE sender_id = ? OR receiver_id = ?
            GROUP BY other_user_id
        ) d
        JOIN messages m ON m.id = d.last_message_id
        JOIN users u    ON u.id = d.other_user_id
        ORDER BY m.created_at DESC
    ";

    $stmt = db_query($sql, [$uid, $uid, $uid]);
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}


