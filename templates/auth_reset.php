<h1>Новый пароль</h1>

<?php if (!empty($error)): ?>
    <div style="color:red;"><?= h($error) ?></div>
<?php endif; ?>

<form method="post" action="/reset">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="token" value="<?= h($token) ?>">

    <label>Пароль:<br>
        <input type="password" name="password" style="width:240px;">
    </label><br><br>

    <label>Повторите пароль:<br>
        <input type="password" name="password2" style="width:240px;">
    </label>

    <div style="margin-top:8px;">
        <input type="submit" value="Сохранить">
    </div>
</form>

<p><a href="/login">← Войти</a></p>
<?php
