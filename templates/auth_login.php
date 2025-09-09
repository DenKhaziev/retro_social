
<h2>Вход</h2>
<?php if (!empty($error)): ?><p style="color:#b00;"><?=e($error)?></p><?php endif; ?>
<form method="post" action="/login">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <div class="form-row">
    <label class="label">Логин (никнейм)</label>
    <input class="input" type="text" name="login">
  </div>
  <div class="form-row">
    <label class="label">Пароль</label>
    <input class="input" type="password" name="password">
  </div>
  <input class="button" type="submit" value="Войти">
</form>
<p><a href="/register">Создать аккаунт</a></p>
<p><a href="/forgot">Забыл пароль</a></p>
