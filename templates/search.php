<!-- search.php -->
<h2>Поиск пользователей</h2>

<!-- Форма поиска -->
<form method="get" action="/search" class="search-form">
    <input type="text" name="query" placeholder="Введите имя или email для поиска" value="<?= isset($_GET['query']) ? e($_GET['query']) : '' ?>" />
    <input type="submit" value="Искать" />
</form>

<!-- Результаты поиска -->
<?php if (isset($users)): ?>
    <h3>Результаты поиска:</h3>
    <ul class="search-results">
        <?php foreach ($users as $user): ?>
            <li>
                <div class="user-info">
                    <a href="/profile/<?= e($user['id']) ?>">
                            <img src="<?= ($user['avatar_path'] ?: '/assets/img/default.jpg')  ?>" alt="<?= e($user['login']) ?>'s avatar" class="user-avatar">
                        <?= e($user['login']) ?> - <?= e($user['email']) ?>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php elseif (isset($error)): ?>
    <p><?= e($error) ?></p>
<?php endif; ?>
