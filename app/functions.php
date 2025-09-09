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
    var_dump($variable);
    echo '</pre>';
}

function find_user(int $id): ?array {
    $stmt = db_query("SELECT id, login FROM users WHERE id = ?", [$id]);
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}

function search_users($query) {
    $query = '%' . $query . '%';

    $stmt = db_query('SELECT id, login, avatar_path, email FROM users WHERE login LIKE ? OR email LIKE ?', [$query, $query]);
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function send_mail_simple(string $to, string $subject, string $body): bool
{

//    $headers  = 'From: ' . MAIL_FROM . "\r\n";
//    $headers .= "Reply-To: no-reply@" . parse_url(APP_BASE_URL, PHP_URL_HOST) . "\r\n";
//    $headers .= "MIME-Version: 1.0\r\n";
//    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
//    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
//
//    return mail($to, $subject, $body, $headers);
    // === лог в файл (как "driver=log") ===
    $dir = dirname(__DIR__) . '/storage'; // проект/storage  (если файл в app/)
    $log = $dir . '/mail.log';

    // Создадим папку, если нет
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $msg  = "---- " . date('Y-m-d H:i:s') . " ----\n";
    $msg .= "To: $to\n";
    $msg .= "Subject: $subject\n";
    $msg .= $body . "\n\n";

    $written = @file_put_contents($log, $msg, FILE_APPEND);

    if ($written === false) {
        // Пробуем упасть в системный tmp
        $tmpLog = rtrim(sys_get_temp_dir(), '/\\') . '/retro_mail.log';
        $writtenTmp = @file_put_contents($tmpLog, $msg, FILE_APPEND);

        // Сообщим в error_log, куда пытались писать
        $err = error_get_last();
        error_log(
            'Mail log write failed to ' . $log .
            '; tmp write ' . ($writtenTmp === false ? 'failed' : 'ok') .
            '; last_error=' . var_export($err, true)
        );
    }

    return true;
}



