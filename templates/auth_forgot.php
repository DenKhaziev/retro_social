<h1>Восстановление пароля</h1>

<?php if (!empty($sent)): ?>
    <div style="color:green;">Если такой e-mail зарегистрирован, мы отправили письмо со ссылкой для сброса.</div>
<?php endif; ?>

<form method="post" action="/forgot">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <label>E-mail:<br>
        <input type="text" name="email" style="width:240px;">
    </label>
    <div style="margin-top:8px;">
        <input type="submit" value="Отправить ссылку">
    </div>
</form>

<p><a href="/login">← Войти</a></p>

