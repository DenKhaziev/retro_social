<?php

require_once __DIR__ . '/db.php';

function ensure_user_upload_dir($uid)
{
    $dir = __DIR__ . '/../public/uploads/' . (int)$uid;
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    return $dir;
}

function public_upload_path_to_url($absPath)
{
    // конвертируем абсолютный путь внутри /public в URL-путь
    $absPublic = realpath(__DIR__ . '/../public');
    $absFile = realpath($absPath);
    if ($absPublic && $absFile && strpos($absFile, $absPublic) === 0) {
        return str_replace(DIRECTORY_SEPARATOR, '/', substr($absFile, strlen($absPublic)));
    }
    return null;
}

function save_uploaded_avatar(array $file, int $uid)
{
    // базовые проверки
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return 'Ошибка загрузки файла';
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return 'Некорректный файл';
    }

    // ограничения
    $maxBytes = 3 * 1024 * 1024; // 3MB
    if ($file['size'] > $maxBytes) {
        return 'Файл слишком большой (макс. 3 МБ)';
    }

    // MIME + размер
    $fi = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fi->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/pjpeg', 'image/gif'], true)) {
        return 'Разрешены только JPG и GIF';
    }
    $info = @getimagesize($file['tmp_name']);
    if (!$info) return 'Не удалось прочитать изображение';
    list($w, $h) = $info;

    // грузим в GD
    if ($mime === 'image/gif') {
        $src = @imagecreatefromgif($file['tmp_name']);
    } else {
        $src = @imagecreatefromjpeg($file['tmp_name']);
    }
    if (!$src) return 'Ошибка декодирования изображения';

    // кроп в квадрат + ресайз до 200x200
    $side = min($w, $h);
    $x = (int)(($w - $side) / 2);
    $y = (int)(($h - $side) / 2);

    $dstSize = 200;
    $dst = imagecreatetruecolor($dstSize, $dstSize);
    imagecopyresampled($dst, $src, 0, 0, $x, $y, $dstSize, $dstSize, $side, $side);

    // путь сохранения
    $dir = ensure_user_upload_dir($uid);
    $fname = 'avatar_' . date('Ymd_His') . '.jpg'; // сохраняем в JPG
    $absPath = $dir . '/' . $fname;

    // качество 85
    imagejpeg($dst, $absPath, 85);
    imagedestroy($src);
    imagedestroy($dst);

    @chmod($absPath, 0664);

    $url = public_upload_path_to_url($absPath); // типа /uploads/123/avatar_20250901_180102.jpg
    if (!$url) return 'Не удалось сформировать URL';

    // запишем в БД путь (относительно /public)
    db_query('UPDATE users SET avatar_path = ?, updated_at = NOW() WHERE id = ?', [$url, $uid]);

    return true;
}
