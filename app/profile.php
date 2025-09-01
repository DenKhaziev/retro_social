<?php

require_once __DIR__ . '/db.php';

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

function profile_update(int $userId, array $data): array
{
    // Валидация
    $errors = [];

    $name = trim((string)($data['name'] ?? ''));
    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Имя обязательно, до 100 символов';
    }

    $gender = $data['gender'] ?? null;
    if ($gender !== null && $gender !== '' && !in_array($gender, ['male', 'female', 'other'], true)) {
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

    // Гарантируем, что запись в user_profiles есть
    $now = date('Y-m-d H:i:s');
    db_query('INSERT IGNORE INTO user_profiles (user_id, name, updated_at) VALUES (?, ?, ?)', [$userId, $name ?: 'Без имени', $now]);

    // Апдейт
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
