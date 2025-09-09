<h1>Блог: <?= h($user['login']) ?></h1>

<?php if ($user['id'] === current_user_id()): ?>
    <form method="get" action="/blog/create" style="margin:10px 0;">
        <input type="submit" value="Написать пост">
    </form>
<?php endif; ?>

<?php foreach ($posts as $p): ?>
    <?php
    $canView = false;
    $isOwner = ($user['id'] === current_user_id());

    if ((int)$p['visibility'] === 0) {
        // публичный
        $canView = true;
    } elseif ((int)$p['visibility'] === 1) {
        // только друзья
        if ($isOwner) {
            $canView = true;
        } else {
            // проверка: текущий юзер друг?
            $canView = are_friends($user['id'], current_user_id());
        }
    } elseif ((int)$p['visibility'] === 2) {
        // приватный
        if ($isOwner) {
            $canView = true;
        }
    }
    ?>

    <?php if ($canView): ?>
        <div style="border:1px solid #ccc; padding:6px; margin:10px 0;">
            <div style="font-weight:bold;">
                <a href="/blog/<?= (int)$p['user_id'] ?>/post/<?= (int)$p['id'] ?>"><?= h($p['title']) ?></a>
            </div>
            <div style="color:#555; font-size:90%; margin:3px 0;">
                <?= h($p['created_at']) ?> · Комм: <?= (int)$p['comments_count'] ?> · Просм: <?= (int)$p['views_count'] ?>
            </div>
            <div><?= nl2br(h(mb_substr($p['body'], 0, 300))) ?>...</div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
