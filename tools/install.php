<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

$root = dirname(__DIR__);
$config = require $root . '/app/config.php';
$db = is_array($config) ? $config['db'] : [
    'host' => DB_HOST, 'user' => DB_USER, 'pass' => DB_PASS,
    'name' => DB_NAME, 'port' => DB_PORT ?? 3306, 'charset' => DB_CHARSET ?? 'utf8mb4',
];

$sql = file_get_contents($root . '/sql/schema.sql');
if ($sql === false) exit("schema.sql not found\n");

$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
if (!$mysqli->real_connect($db['host'], $db['user'], $db['pass'], $db['name'], (int)$db['port'])) {
    exit('DB connect error: '.mysqli_connect_error()."\n");
}
$mysqli->set_charset($db['charset'] ?? 'utf8mb4');

if (!$mysqli->multi_query($sql)) {
    exit("SQL error: ".$mysqli->error."\n");
}
do {
    if ($result = $mysqli->store_result()) { $result->free(); }
} while ($mysqli->more_results() && $mysqli->next_result());

if ($mysqli->errno) {
    exit("FAILED: ".$mysqli->error."\n");
}
echo "OK: schema installed/updated.\n";
