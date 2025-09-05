<h1 style="margin-bottom: 10px">Комментарии к фото</h1>

<div style="width: 100px; height: 100px; overflow: hidden; border: 1px solid #ccc; text-align: center; vertical-align: middle; margin-bottom: 10px">
    <a href="/<?= htmlspecialchars($photo['file_path']) ?>" target="_blank">
        <img src="/<?= htmlspecialchars($photo['file_path']) ?>" alt="Фото" style="max-width: 100px; max-height: 100px;">
    </a>
</div>
<?php if (empty($comments)): ?>
    <p>Комментариев нет.</p>
<?php else: ?>
    <?php foreach ($comments as $c): ?>
        <div style="border:1px solid #ccc; padding:5px; margin:8px 0; overflow:hidden;">
            <!-- аватар + логин слева -->
            <div style="float:left; width:60px; text-align:center; margin-right:8px;">
                <img src="<?= h($c['avatar_path'] ?: '/assets/img/default.jpg') ?>" width="40" height="40" alt="">
                <a href="/profile/<?= h($c['user_id']) ?>" style="font-size:80%;"><?= h($c['login']) ?></a>
            </div>

            <!-- текст и дата -->
            <div style="margin-left:70px;">
                <div style="font-size:90%; color:#555; text-align:right;">
                    <?= h($c['created_at']) ?>
                </div>
                <div><?= nl2br(h($c['body'])) ?></div>
            </div>

            <div style="clear:both;"></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<h2>Добавить комментарий</h2>
<form method="post" action="/photos/<?= (int)$owner_id ?>/photo/<?= (int)$photo['id'] ?>/comment/add">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <textarea name="body" rows="4" cols="40"></textarea><br>
    <input type="submit" value="Отправить">
</form>

<p style="margin-top: 20px"><a href="/photos/<?= (int)$owner_id ?>">← Назад к фотографиям</a></p>
