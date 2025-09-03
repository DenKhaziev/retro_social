<?php

require_once __DIR__ . '/db.php';

// Функция для получения данных профиля
function get_user_profile(int $userId): array
{
    // Запрос данных профиля пользователя
    $stmt = db_query('
        SELECT u.id, u.login, u.avatar_path,
               COALESCE(p.name, u.login) AS name,
               p.gender, p.birthdate, p.location, p.website, p.bio
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        WHERE u.id = ? LIMIT 1
    ', [$userId]);
    $user = $stmt->get_result()->fetch_assoc();

    // Получаем дополнительные данные (друзья, фото, сообщения)
    $friendsCount = 0;
    $stmt = db_query("SELECT COUNT(*) AS c FROM friendships WHERE status='accepted' AND (user_id=? OR friend_id=?)", [$userId, $userId]);
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) $friendsCount = (int)$row['c'];

    $photosCount = 0;
    $stmt = db_query("SELECT COUNT(*) AS c FROM photos WHERE user_id=?", [$userId]);
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) $photosCount = (int)$row['c'];

    $unreadCount = 0;
    $stmt = db_query("SELECT COUNT(*) AS c FROM messages WHERE receiver_id=? AND read_at IS NULL", [$userId]);
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) $unreadCount = (int)$row['c'];

    // Возвращаем все данные профиля
    return [
        'user' => $user,
        'friendsCount' => $friendsCount,
        'photosCount' => $photosCount,
        'unreadCount' => $unreadCount,
    ];
}

// Функция для обновления профиля
function profile_update(int $userId, array $data): array
{
    $errors = [];

    // Валидация
    $name = trim((string)($data['name'] ?? ''));
    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Имя обязательно, до 100 символов';
    }

    $gender = $data['gender'] ?? null;
    if ($gender !== null && !in_array($gender, ['male', 'female', 'other'], true)) {
        $errors[] = 'Некорректный пол';
    }

    $birthdate = trim((string)($data['birthdate'] ?? ''));
    if ($birthdate !== '' && !preg_match('~^\d{4}-\d{2}-\d{2}$~', $birthdate)) {
        $errors[] = 'Дата рождения в формате ГГГГ-ММ-ДД';
    }

    $location = trim((string)($data['location'] ?? ''));
    if (mb_strlen($location) > 120) {
        $errors[] = 'Город до 120 символов';
    }

    $website = trim((string)($data['website'] ?? ''));
    if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
        $errors[] = 'Некорректный URL сайта';
    }
    if (mb_strlen($website) > 190) {
        $errors[] = 'Сайт до 190 символов';
    }

    $bio = trim((string)($data['bio'] ?? ''));
    if (mb_strlen($bio) > 255) {
        $errors[] = 'О себе до 255 символов';
    }

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    // Запись в user_profiles (если записи нет, то добавляем, если есть — обновляем)
    $now = date('Y-m-d H:i:s');
    db_query('
        INSERT INTO user_profiles (user_id, name, updated_at)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE name = ?, updated_at = ?
    ', [$userId, $name ?: 'Без имени', $now, $name, $now]);

    // Апдейт данных
    db_query('
        UPDATE user_profiles
        SET name=?, gender=?, birthdate=IF(?="", NULL, ?),
            location=?, website=?, bio=?, updated_at=?
        WHERE user_id=?
    ', [
        $name,
        ($gender === '' ? null : $gender),
        $birthdate, $birthdate,
        $location, $website, $bio, $now,
        $userId
    ]);

    return ['ok' => true];
}

function profile_find(int $userId): ?array
{
    $stmt = db_query('
        SELECT u.id, u.login, u.avatar_path,
               COALESCE(p.name, u.login) AS name,
               p.gender, p.birthdate, p.location, p.website, p.bio, p.updated_at
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        WHERE u.id = ? LIMIT 1
    ', [$userId]);
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}
