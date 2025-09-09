<?php

//pretty_var_dump($user);
// Функция для генерации кнопок
function render_buttons($photo, $is_user_photo = false) {
    $photoId = (int)$photo['id'];
    $likesCount = (int)$photo['likes_count'];
    $commentsCount = (int)$photo['comments_count'];

    if ($is_user_photo) {
        // Лайки
        echo '<form method="get" action="/photos/' . (int)$photo['user_id'] . '/photo/' . $photoId . '/likes" style="display:inline">';
        echo '<input type="submit" value="Лайки (' . $likesCount . ')">';
        echo '</form> ';

        // Кнопка "Комментарии" (просмотр списка)
        echo '<form method="get" action="/photos/' . (int)$photo['user_id'] . '/photo/' . (int)$photo['id'] . '/comments" style="display:inline">';
        echo '<input type="submit" value="Комментарии (' . $commentsCount . ')">';
        echo '</form> ';

        // Удалить (форма)
        echo '<form method="post" action="/photos/delete/' . $photoId . '" style="display:inline">';
        echo '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
        echo '<input type="submit" value="Удалить">';
        echo '</form>';
    } else {
        // Лайк
        echo '<form method="post" action="/photos/like/' . $photoId . '" style="display:inline">';
        echo '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
        echo '<input type="submit" value="Лайк (' . $likesCount . ')">';
        echo '</form> ';

        // Кнопка "Комментарии" (просмотр списка)
        echo '<form method="get" action="/photos/' . (int)$photo['user_id'] . '/photo/' . (int)$photo['id'] . '/comments" style="display:inline">';
        echo '<input type="submit" value="Комментарии">';
        echo '</form> ';
    }
}

?>

    <h1 style="margin-bottom: 10px">
        <?php if ($isOwner): ?>
            Мои фотографии
        <?php else: ?>
            Фотографии пользователя <?= $user['login'] ?>
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
