
<h2>Регистрация</h2>
<?php if (!empty($error)): ?><p style="color:#b00;"><?=e($error)?></p><?php endif; ?>
<form method="post" action="/register">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <div class="form-row">
    <label class="label">Логин (никнейм)</label>
    <input class="input" type="text" name="login" maxlength="32">
  </div>
  <div class="form-row">
    <label class="label">Email</label>
    <input class="input" type="text" name="email" maxlength="190">
  </div>
  <div class="form-row">
    <label class="label">Пароль</label>
    <input class="input" type="password" name="password">
  </div>
  <input class="button" type="submit" value="Создать аккаунт">
</form>
<p><a href="/login">У меня уже есть аккаунт</a></p>
