<?php


require_once __DIR__ . '/db.php';  // Подключаем файл с базой данных (предполагается, что db.php есть)

function generate_random_user_data() {
    $names = ['John', 'Jane', 'Alice', 'Bob', 'Charlie'];
    $genders = ['male', 'female', 'other'];
    $locations = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
    $websites = ['https://example.com', 'https://test.com', 'https://website.com', 'https://blog.com', 'https://profile.com'];
    $bios = ['Loves coding', 'Photographer', 'Designer', 'Developer', 'Gamer'];

    $name = $names[array_rand($names)];
    $gender = $genders[array_rand($genders)];
    $location = $locations[array_rand($locations)];
    $website = $websites[array_rand($websites)];
    $bio = $bios[array_rand($bios)];

    // Уникальный логин и email с рандомным числом
    $unique_number = rand(1000, 9999);
    return [
        'login' => strtolower($name) . $unique_number,  // создаем уникальный логин
        'email' => strtolower($name) . $unique_number . '@example.com', // уникальный email
        'password_hash' => password_hash('password123', PASSWORD_DEFAULT), // хешируем пароль
        'name' => $name,
        'gender' => $gender,
        'birthdate' => date('Y-m-d', strtotime(rand(18, 40) . ' years ago')),
        'location' => $location,
        'website' => $website,
        'bio' => $bio,
    ];
}

function seed_users()
{
    // Генерируем 5 пользователей с рандомными данными
    for ($i = 0; $i < 5; $i++) {
        $user_data = generate_random_user_data();

        // Вставляем пользователя в таблицу users
        $stmt = db_query('INSERT INTO users (login, email, password_hash, avatar_path, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())', [
            $user_data['login'],
            $user_data['email'],
            $user_data['password_hash'],
            '', // Путь к аватару, если нужно, можно добавить позже
        ]);

        // Получаем последний вставленный id пользователя
        $user_id = db()->insert_id;

        // Вставляем данные в таблицу user_profiles
        db_query('INSERT INTO user_profiles (user_id, name, gender, birthdate, location, website, bio, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())', [
            $user_id,
            $user_data['name'],
            $user_data['gender'],
            $user_data['birthdate'],
            $user_data['location'],
            $user_data['website'],
            $user_data['bio'],
        ]);
    }
    echo "Сидеры для пользователей добавлены!\n";
}

// Запускаем сидер
seed_users();
