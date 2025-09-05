<?php

function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
function render($tpl, $vars = []) {
    extract($vars, EXTR_SKIP);
    include __DIR__.'/../templates/'.$tpl.'.php';
}
function redirect($url) {
    header('Location: '.$url); exit;
}

function pretty_var_dump($variable) {
    echo '<pre style="background-color: #f4f4f4; padding: 10px; border-radius: 5px; font-family: monospace; color: #333;">';
    var_dump($variable);  // Это основной вывод переменной
    echo '</pre>';
}

function find_user(int $id): ?array {
    $stmt = db_query("SELECT id, login FROM users WHERE id = ?", [$id]);
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}

function search_users($query) {
    // Очистка данных от потенциальных SQL инъекций
    $query = '%' . $query . '%';

    // Поиск по имени и email
    $stmt = db_query('SELECT id, login, avatar_path, email FROM users WHERE login LIKE ? OR email LIKE ?', [$query, $query]);
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}



