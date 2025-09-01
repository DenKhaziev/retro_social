<?php
$config = require __DIR__.'/config.php';
$cfg = $config['db'];

$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
if (!$mysqli->real_connect($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name'], $cfg['port'])) {
    die('DB connect error: '.mysqli_connect_error());
}
$mysqli->set_charset($cfg['charset']);

// Пример хелпера prepare/exec:
function db() {
    global $mysqli; return $mysqli;
}
function db_query($sql, $params = array()) {
    $stmt = db()->prepare($sql);
    if ($params) {
        // строка типов для bind_param, все как строки 's' для простоты
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}