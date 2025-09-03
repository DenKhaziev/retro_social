<?php

//pretty_var_dump($currentUserId);
// Функция для генерации кнопок
function render_buttons($photo, $is_user_photo = false) {
    if ($is_user_photo) {
        // Кнопки для своей фотки
        echo '<button style="cursor: pointer" data-photo-id="' . htmlspecialchars($photo['id']) . '">Лайки ' . $photo['likes_count'] . '</button>';
        echo '<button style="cursor: pointer" data-photo-id="' . htmlspecialchars($photo['id']) . '">Комментарии</button>';
        echo '<button style="cursor: pointer" data-photo-id="' . htmlspecialchars($photo['id']) . '">Удалить</button>';
    } else {
        // Кнопки для чужой фотки
        echo '<button style="cursor: pointer" data-photo-id="' . htmlspecialchars($photo['id']) . '">Лайк ' . $photo['likes_count'] . '</button>';
        echo '<button style="cursor: pointer" data-photo-id="' . htmlspecialchars($photo['id']) . '">Оставить комментарий</button>';
    }
}

?>

    <h1>
        <?php if ($isOwner): ?>
            Мои фотографии
        <?php else: ?>
            Фотографии пользователя <?= htmlspecialchars($user['login']) ?>
        <?php endif; ?>
    </h1>

    <?php if ($photos): ?>
        <div class="photo-grid">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-card">
                    <div class="img-wrapper">
                        <a href="/<?= htmlspecialchars($photo['file_path']) ?>" target="_blank">
                            <img src="/<?= htmlspecialchars($photo['file_path']) ?>" alt="Фото">
                        </a>
                    </div>
                    <div class="photo-actions">
                        <?php
                        // Проверка, чья это фотка
                        $is_user_photo = ($photo['user_id'] === $uid);
                        render_buttons($photo, $is_user_photo);
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="border: solid 1px black; padding: 2px; width: 105px">Нет фотографий</p>
    <?php endif; ?>

<!-- Форма для загрузки фото -->
<div class="upload-form">
    <h3>Загрузить фото</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="file" name="photo" required>
        <input type="submit" value="Загрузить">
    </form>
</div>
