<?php
$config = [
    'db' => [
        'host'    => '127.0.0.1',
        'user'    => 'retro_user',
        'pass'    => 'secret',
        'name'    => 'retro_social',
        'port'    => 3306,
        'charset' => 'utf8mb4',
    ],
    'site_name' => 'Retro Social',
    'base_url'  => 'http://localhost:8002',
    'timezone'  => 'UTC',
    'mail_from' => 'Retro Social <noreply@example.com>',
];

if (!defined('APP_BASE_URL')) {
    define('APP_BASE_URL', $config['base_url']);
}
if (!defined('MAIL_FROM')) {
    define('MAIL_FROM', $config['mail_from']);
}

return $config;
