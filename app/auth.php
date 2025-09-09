<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/input.php';

function current_user_id()
{
    start_session_once();
    return isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
}

function login_required()
{
    if (!current_user_id()) {
        header('Location: /login');
        exit;
    }
}

function login($login, $password)
{
    $stmt = db_query('SELECT id,password_hash FROM users WHERE login=? LIMIT 1', [$login]);
    $u = $stmt->get_result()->fetch_assoc();
    if (!$u) return false;
    if (!password_verify($password, $u['password_hash'])) return false;

    start_session_once();
    $_SESSION['uid'] = (int)$u['id'];
    db_query('UPDATE users SET updated_at=NOW() WHERE id=?', [$u['id']]);
    return true;
}

function logout()
{
    start_session_once();
    $_SESSION = [];
    session_destroy();
}

function register_user($login, $email, $password)
{
    // простая валидация
    if ($login === '' || $email === '' || $password === '') return 'Заполните все поля';
    if (!preg_match('~^[a-zA-Z0-9_]{3,32}$~', $login)) return 'Логин: 3–32, латиница/цифры/подчёркивание';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Некорректный email';

    // уникальность
    $stmt = db_query('SELECT id FROM users WHERE login=? OR email=? LIMIT 1', [$login, $email]);
    if ($stmt->get_result()->fetch_assoc()) return 'Логин или email уже заняты';

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');

    db_query('INSERT INTO users (login,email,password_hash,avatar_path,created_at,updated_at) VALUES (?,?,?,?,?,?)',
        [$login, $email, $hash, '', $now, $now]);


    $uid = db()->insert_id;
    db_query('INSERT INTO user_profiles (user_id,name,updated_at) VALUES (?,?,?)', [$uid, $login, $now]);


    start_session_once();
    $_SESSION['uid'] = (int)$uid;

    return true;
}

function get_profile_user_id(): ?int {

    if (isset($_GET['user_id'])) {
        return (int)$_GET['user_id'];
    }

    $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (preg_match('#^/photos/(\d+)$#', $uriPath, $m)) {
        return (int)$m[1];
    }

    return current_user_id();
}
