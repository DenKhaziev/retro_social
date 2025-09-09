<!-- список постов /blog/{user_id} -->
<h1>Блог: <?= h($user['login']) ?></h1>

<?php foreach ($posts as $p): ?>
    <div style="border:1px solid #ccc; padding:6px; margin:10px 0;">
        <div style="font-weight:bold;">
            <a href="/blog/<?= (int)$p['user_id'] ?>/post/<?= (int)$p['id'] ?>"><?= h($p['title']) ?></a>
        </div>
        <div style="color:#555; font-size:90%; margin:3px 0;">
            <?= h($p['created_at']) ?> · Комм: <?= (int)$p['comments_count'] ?> · Просм: <?= (int)$p['views_count'] ?>
            <?php if (isset($p['likes_count'])): ?> · Лайки: <?= (int)$p['likes_count'] ?><?php endif; ?>
        </div>
        <div><?= nl2br(h(mb_substr($p['body'], 0, 300))) ?>...</div>
    </div>
<?php endforeach; ?><?php
