

<h1 style="margin-bottom: 10px">
    <?php if ($isOwner): ?>
        Мои друзья
    <?php else: ?>
        Друзья пользователя <?= h($user['login']) ?>
    <?php endif; ?>
</h1>

<?php if (empty($friends)): ?>
    <p>Пока нет друзей.</p>
<?php else: ?>
    <table class="friend-table" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <th width="60">Аватар</th>
            <th>Никнейм</th>
            <th width="150">Профиль</th>
        </tr>
        <?php foreach ($friends as $friend): ?>
            <tr>
                <td align="center">
                    <img src="<?= h($friend['avatar_path'] ?: '/assets/img/default.jpg') ?>" alt="">
                </td>
                <td class="login"><?= h($friend['login']) ?></td>
                <td><a href="/profile/<?= (int)$friend['friend_id'] ?>">Открыть</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php if ($isOwner && $pending):?>
    <h2 style="margin-bottom: 10px">Заявки в друзья</h2>
    <table class="friend-table" border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th width="60">Аватар</th>
            <th>Никнейм</th>
            <th width="150">Действие</th>
        </tr>
        <?php foreach ($pending as $p): ?>
            <tr>
                <td><img src="<?= h($p['avatar_path']?: '/assets/img/default.jpg') ?>" width="40" height="40" alt=""></td>
                <td><a href="/profile/<?= (int)$p['requester_id'] ?>"><?= h($p['login']) ?></a></td>
                <td>
                    <a href="/friends/accept/<?= (int)$p['requester_id'] ?>">Принять</a> |
                    <a href="/friends/decline/<?= (int)$p['requester_id'] ?>">Отклонить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<p style="margin-top: 20px"><a href="/profile/<?= (int)$user['id'] ?>">← Назад в профиль</a></p>
