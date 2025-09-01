<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$root = dirname(__DIR__);
$config = require $root . '/app/config.php';
$db = $config['db'];

$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name'], $db['port']);
if ($mysqli->connect_error) exit("DB connect error: {$mysqli->connect_error}\n");
$mysqli->set_charset($db['charset'] ?? 'utf8mb4');

$mysqli->query("CREATE TABLE IF NOT EXISTS schema_migrations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(190) NOT NULL UNIQUE,
  applied_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$applied = [];
$res = $mysqli->query("SELECT filename FROM schema_migrations");
while ($row = $res->fetch_assoc()) $applied[$row['filename']] = true;

$dir = $root . '/sql/migrations';
$files = glob($dir . '/*.sql');
sort($files, SORT_NATURAL);

$mysqli->begin_transaction();

try {
    foreach ($files as $path) {
        $name = basename($path);
        if (isset($applied[$name])) continue;

        $sql = file_get_contents($path);
        if (!$mysqli->multi_query($sql)) {
            throw new Exception("Error in $name: " . $mysqli->error);
        }
        // вычистить буферы результатов
        while ($mysqli->more_results() && $mysqli->next_result()) {
            if ($r = $mysqli->store_result()) $r->free();
        }
        if ($mysqli->errno) throw new Exception("Error in $name: " . $mysqli->error);

        $stmt = $mysqli->prepare("INSERT INTO schema_migrations (filename, applied_at) VALUES (?, NOW())");
        $stmt->bind_param('s', $name);
        $stmt->execute();
    }
    $mysqli->commit();
    echo "OK: migrations applied.\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
