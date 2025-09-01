<?php
// Получаем данные текущего пользователя
$uid = current_user_id();

$u = null;
$stmt = db_query('
    SELECT u.id, u.login, u.avatar_path, u.email,
           COALESCE(p.name, u.login) AS name,
           p.location, p.website, p.bio
    FROM users u
    LEFT JOIN user_profiles p ON p.user_id = u.id
    WHERE u.id = ? LIMIT 1
', array($uid));
$u = $stmt->get_result()->fetch_assoc();

// Счётчики
$friendsCount = 0;
$stmt = db_query("SELECT COUNT(*) AS c FROM friendships WHERE status='accepted' AND (user_id=? OR friend_id=?)", array($uid,$uid));
$row = $stmt->get_result()->fetch_assoc(); if ($row) $friendsCount = (int)$row['c'];

$photosCount = 0;
$stmt = db_query("SELECT COUNT(*) AS c FROM photos WHERE user_id=?", array($uid));
$row = $stmt->get_result()->fetch_assoc(); if ($row) $photosCount = (int)$row['c'];

$unreadCount = 0;
$stmt = db_query("SELECT COUNT(*) AS c FROM messages WHERE receiver_id=? AND read_at IS NULL", array($uid));
$row = $stmt->get_result()->fetch_assoc(); if ($row) $unreadCount = (int)$row['c'];

// Утилитки
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<div class="cols"><!-- clearfix -->

    <!-- Левая колонка: табы -->
    <div class="col-left">
        <div class="card">
            <div class="card-h">Навигация</div>
            <div class="card-b">
                <ul class="tab-nav">
                    <li><a href="/user/<?= (int)$uid ?>">Профиль</a></li>
                    <li><a href="/photos">Фотографии <span class="badge"><?= $photosCount ?></span></a></li>
                    <li><a href="/friends">Друзья <span class="badge"><?= $friendsCount ?></span></a></li>
                    <li><a href="/messages">Сообщения <span class="badge"><?= $unreadCount ?></span></a></li>
                    <li><a href="/search">Поиск людей</a></li>
                    <li><a href="/settings">Настройки</a></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-h">Поиск</div>
            <div class="card-b">
                <form method="get" action="/search">
                    <div class="form-row">
                        <label class="label">Имя/логин</label>
                        <input class="input" type="text" name="q" value="">
                    </div>
                    <input class="button" type="submit" value="Найти">
                </form>
            </div>
        </div>
    </div>


    <!-- Центр: квадратное фото -->
    <div class="col-center">
        <div class="card">
            <div class="card-h">Фотография профиля</div>
            <div class="card-b">
                <div class="avatar-box">
                    <?php if (!empty($u['avatar_path'])): ?>
                        <img src="<?= h($u['avatar_path']) ?>" alt="Аватар" width="200" height="200">
                    <?php else: ?>
                        <div class="avatar-placeholder">Нет фото</div>
                    <?php endif; ?>
                </div>

                <p class="mt8">
                    <a class="button" href="/settings">Загрузить/сменить фото</a>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-h">Коротко</div>
            <div class="card-b">
                <ul class="stats">
                    <li><b><?= $friendsCount ?></b> друзей</li>
                    <li><b><?= $photosCount ?></b> фото</li>
                    <li><b><?= $unreadCount ?></b> непроч. сообщений</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Правая: личные данные -->
    <div class="col-right">
        <div class="card">
            <div class="card-h"><?= h($u ? $u['name'] : 'Пользователь') ?></div>
            <div class="card-b">
                <dl class="info-list">
                    <dt>e-mail:</dt><dd><?= h($u['email']) ?></dd>
                    <?php if (!empty($u['location'])): ?>
                        <dt>Город:</dt><dd><?= h($u['location']) ?></dd>
                    <?php endif; ?>
                    <?php if (!empty($u['website'])): ?>
                        <dt>Сайт:</dt><dd><a href="<?= h($u['website']) ?>" target="_blank"><?= h($u['website']) ?></a></dd>
                    <?php endif; ?>
                    <?php if (!empty($u['bio'])): ?>
                        <dt>О себе:</dt><dd><?= h($u['bio']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-h">Быстрые ссылки</div>
            <div class="card-b">
                <ul class="link-list">
                    <li><a href="/user/<?= (int)$uid ?>">Мой профиль</a></li>
                    <li><a href="/friends">Мои друзья</a></li>
                    <li><a href="/photos">Мои фото</a></li>
                    <li><a href="/messages">Личные сообщения</a></li>
                </ul>
            </div>
        </div>
    </div>

</div><!-- /.cols -->
