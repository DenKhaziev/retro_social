
<h1 style="margin-bottom: 10px">Список лайков</h1>

<div style="width: 100px; height: 100px; overflow: hidden; border: 1px solid #ccc; text-align: center; vertical-align: middle; margin-bottom: 10px">
    <a href="/<?= htmlspecialchars($photo['file_path']) ?>" target="_blank">
        <img src="/<?= htmlspecialchars($photo['file_path']) ?>" alt="Фото" style="max-width: 100px; max-height: 100px;">
    </a>
</div>
<?php if (empty($likes)): ?>
    <p>Нет лайков</p>
<?php else: ?>
    <table class="friend-table" border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th width="60">Аватар</th>
            <th>Логин</th>
            <th width="150">Профиль</th>
        </tr>
        <?php foreach ($likes as $user): ?>
            <tr>
                <td align="center">
                        <img src="<?= h($user['avatar_path'] ?: '/assets/img/default.jpg') ?>" width="40" height="40" alt="">
                </td>
                <td><?= h($user['login']) ?></td>
                <td><a href="/profile/<?= (int)$user['id'] ?>">Открыть</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<p style="margin-top: 20px"><a href="/profile/<?= (int)$user['id'] ?>">← Назад в профиль</a></p>
