<?php
$currentUserId = current_user_id();
$isOwner = $currentUserId === $uid;
$viewedUser     = $profileData['user'];
$friendsCount   = $profileData['friendsCount'];
$photosCount    = $profileData['photosCount'];
$unreadCount    = $profileData['unreadCount'];
$myFriendsCount = $sidebarStats['friendsCount'];
$myPhotosCount  = $sidebarStats['photosCount'];
$myUnreadCount  = $sidebarStats['unreadCount'];

?>

<div class="cols"><!-- clearfix -->

    <!-- Левая колонка: табы залогиненного пользователя -->
    <div class="col-left">
        <div class="card">
            <div class="card-h">Навигация</div>
            <div class="card-b">
                <ul class="tab-nav">
                    <li><a href="/profile/show/<?= (int)$currentUserId ?>">Профиль (ред.) </a></li>
                    <li><a href="/photos/<?= (int)$currentUserId ?>">Фотографии <span class="badge"><?= $myPhotosCount ?></span></a></li>
                    <li><a href="/friends/<?= (int)$currentUserId ?>">Друзья <span class="badge"><?= $myFriendsCount ?></span></a></li>
                    <li><a href="/messages"">Сообщения <span class="badge"><?= $myUnreadCount ?></span></a></li>
                    <li><a href="/search">Поиск людей</a></li>
                    <li><a href="/settings">Настройки</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Центр: аватар -->
    <div class="col-center">
        <div class="card">
            <div class="card-h">Фотография профиля</div>
            <div class="card-b">
                <div class="avatar-box">
                    <?php if (!empty($viewedUser['avatar_path'])): ?>
                        <img src="<?= h($viewedUser['avatar_path']) ?>" alt="Аватар" width="200" height="200">
                    <?php else: ?>
                        <img src="/assets/img/default.jpg" alt="">
                    <?php endif; ?>
                </div>

                <?php if ($isOwner): ?>
                    <p class="mt8">
                        <a class="button" href="/settings">Загрузить/сменить фото</a>
                    </p>
                <?php else: ?>
                    <p class="mt8">
                        <a class="button" href="/messages/<?= (int)$viewedUser['id'] ?>">Написать сообщение</a>
                    </p>
                    <?php if ($uid !== current_user_id()): ?>
                        <?php
                        $status = get_friendship_status(current_user_id(), $uid);
                        if ($status === null): ?>
                            <form method="post" action="/friends/add">
                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="friend_id" value="<?= (int)$uid ?>">
                                <input class="button" type="submit" value="Добавить в друзья">
                            </form>
                        <?php elseif ($status === 'pending'): ?>
                            <p class="button">Запрос отправлен</p>
                        <?php elseif ($status === 'accepted'): ?>
                            <p class="button">Уже в друзьях</p>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-h">Коротко</div>
            <div class="card-b">
                <ul class="stats">
                    <?php if ($isOwner): ?>
                        <li><b><?= $friendsCount ?></b> друзей</li>
                        <li><b><?= $photosCount ?></b> фото</li>
                        <li><b><?= $unreadCount ?></b> непроч. сообщений</li>
                    <?php else: ?>
                        <a href="/friends/<?= (int)$viewedUser['id'] ?>"><li><b><?= $friendsCount ?></b> друзей</li></a>
                        <a href="/photos/<?= (int)$viewedUser['id'] ?>"><b><?= $photosCount ?></b> фото</a>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Правая колонка: профиль пользователя -->
    <div class="col-right">
        <div class="card">
            <div class="card-h"><?= h($viewedUser['name']) ?></div>
            <div class="card-b">
                <dl class="info-list">
                    <dt>e-mail:</dt><dd><?= h($viewedUser['email']) ?></dd>
                    <?php if (!empty($viewedUser['location'])): ?>
                        <dt>Город:</dt><dd><?= h($viewedUser['location']) ?></dd>
                    <?php endif; ?>
                    <?php if (!empty($viewedUser['website'])): ?>
                        <dt>Сайт:</dt><dd><a href="<?= h($viewedUser['website']) ?>" target="_blank"><?= h($viewedUser['website']) ?></a></dd>
                    <?php endif; ?>
                    <?php if (!empty($viewedUser['bio'])): ?>
                        <dt>О себе:</dt><dd><?= h($viewedUser['bio']) ?></dd>
                    <?php endif; ?>
                </dl>
                <?php if ($isOwner): ?>
                    <p class="mt8"><a class="button" href="/profile/edit">Редактировать</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div><!-- /.cols -->
