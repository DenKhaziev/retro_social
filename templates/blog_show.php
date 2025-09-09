<h1><?= h($post['title']) ?></h1>

<div style="color:#555; font-size:90%; margin:4px 0;">
    <?= h($post['created_at']) ?> · Комм: <?= (int)$post['comments_count'] ?> · Просм: <?= (int)$post['views_count'] ?>
</div>

<div style="border:1px solid #ccc; padding:8px; margin:8px 0;">
    <?= nl2br(h($post['body'])) ?>
</div>

<?php if (!empty($isOwner)): ?>
    <form method="post" action="/blog/delete/<?= (int)$post['id'] ?>" style="display:inline">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="submit" value="Удалить пост">
    </form>
<?php endif; ?>

<p style="margin-top: 10px"><a href="/blog/<?= (int)$post['user_id'] ?>">← Назад к блогу</a></p>

<hr>

<h2>Комментарии</h2>

<?php if (empty($comments)): ?>
    <p>Комментариев нет.</p>
<?php else: ?>
    <?php foreach ($comments as $c): ?>
        <div style="border:1px solid #ccc; padding:5px; margin:8px 0; overflow:hidden;">
            <!-- Аватар + логин слева -->
            <div style="float:left; width:60px; text-align:center; margin-right:8px;">
                <img src="<?= h($c['avatar_path'] ?: '/assets/img/no-avatar.png') ?>" width="40" height="40" alt="">
                <div style="font-size:80%;"><?= h($c['login']) ?></div>
            </div>
            <!-- Текст + дата справа -->
            <div style="margin-left:70px;">
                <div style="font-size:90%; color:#555; text-align:right;"><?= h($c['created_at']) ?></div>
                <div><?= nl2br(h($c['body'])) ?></div>
            </div>
            <div style="clear:both;"></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<h3>Добавить комментарий</h3>
<form method="post" action="/blog/<?= (int)$post['user_id'] ?>/post/<?= (int)$post['id'] ?>/comment/add">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <textarea name="body" rows="5" cols="60"></textarea><br>
    <input type="submit" value="Отправить">
</form>
<?php
