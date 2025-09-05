<?php

// Получаем данные о фотографиях
function get_photos(int $userId): array
{
    // Запрос для получения всех фотографий данного пользователя
    $stmt = db_query('
        SELECT p.id, p.user_id, p.file_path, p.caption, p.created_at, 
               COUNT(pl.user_id) AS likes_count
        FROM photos p
        LEFT JOIN photo_likes pl ON p.id = pl.photo_id
        WHERE p.user_id = ?
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ', [$userId]);


    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function upload_photo(array $file, int $userId): string
{
    // 1) базовые проверки
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return 'Ошибка загрузки файла';
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return 'Некорректный файл';
    }

    // 2) лимиты и типы
    $maxBytes = 10 * 1024 * 1024; // 10MB
    if (!isset($file['size']) || $file['size'] > $maxBytes) {
        return 'Файл слишком большой (макс. 10 МБ)';
    }

    $fi = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fi->file($file['tmp_name']);
    // PNG тоже разрешим для обычных фото
    $allowedMimes = ['image/jpeg','image/pjpeg','image/png','image/gif'];
    if (!in_array($mime, $allowedMimes, true)) {
        return 'Разрешены только JPG, PNG и GIF';
    }

    // 3) целевая директория: uploads/photos/{userId}
    $relDir = 'uploads/photos/' . (int)$userId;
    $absDir = __DIR__ . '/../public/' . $relDir;
    if (!is_dir($absDir)) {
        if (!@mkdir($absDir, 0775, true) && !is_dir($absDir)) {
            return 'Не удалось создать директорию загрузки';
        }
    }

    // 4) безопасное имя файла (оставим расширение по MIME)
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/pjpeg'=> 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
    ];
    $ext = $extMap[$mime] ?? 'jpg';
    $fname = 'ph_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    $relPath = $relDir . '/' . $fname;                 // например: uploads/photos/42/ph_20250903_123456_ab12cd34.jpg
    $absPath = __DIR__ . '/../public/' . $relPath;

    // 5) сохраняем как есть (без кропа/ресайза, сохраняем исходное соотношение)
    if (!move_uploaded_file($file['tmp_name'], $absPath)) {
        return 'Не удалось сохранить файл';
    }
    @chmod($absPath, 0664);

    // 6) запись в БД относительного пути (относительно /public)
    db_query('INSERT INTO photos (user_id, file_path, caption, created_at) VALUES (?, ?, ?, NOW())', [
        $userId,
        $relPath,
        '' // caption пока пустой (можно добавить поле в форму)
    ]);

    // успех — вернём относительный путь (можно использовать для предпросмотра)
    return '/' . $relPath;
}

function photo_find(int $photoId): ?array
{
    $stmt = db_query("SELECT id, user_id, file_path FROM photos WHERE id = ? LIMIT 1", [$photoId]);
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}

/**
 * Полное удаление фотографии с проверкой прав.
 * Возвращает ['ok' => bool, 'error' => string|null]
 */
function photo_delete(int $photoId, int $currentUserId): array
{
    $photo = photo_find($photoId);
    if (!$photo) {
        return ['ok' => false, 'error' => 'not_found'];
    }

    if ((int)$photo['user_id'] !== (int)$currentUserId) {
        return ['ok' => false, 'error' => 'forbidden'];
    }

    db_query("DELETE FROM photo_likes WHERE photo_id = ?", [$photoId]);

    db_query("DELETE FROM photos WHERE id = ?", [$photoId]);

    $root = dirname(__DIR__); // /app -> проект
    $filePath = $root . '/public/' . ltrim($photo['file_path'], '/');

    if (is_file($filePath)) {
        @unlink($filePath);
    }

    return ['ok' => true, 'error' => null];
}