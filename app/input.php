<?php

function start_session_once()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function csrf_token()
{
    start_session_once();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_check($token)
{
    start_session_once();
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}

function e($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function post($key, $default = '')
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}
