
<h2>Настройки</h2>

<div class="cols">
  <div class="col-left">
    <div class="card">
      <div class="card-h">Меню</div>
      <div class="card-b">
        <ul class="tab-nav">
          <li><a href="/profile">Мой профиль</a></li>
<!--          <li><a href="/settings">Уведомления</a></li>-->
        </ul>
      </div>
    </div>
  </div>

  <div class="col-center">
    <div class="card">
      <div class="card-h">Аватар</div>
      <div class="card-b">
        <?php if (!empty($error)): ?>
          <p style="color:#b00;"><?= e($error) ?></p>
        <?php endif; ?>
        <form method="post" action="/settings/avatar" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <div class="form-row">
            <label class="label">Файл (JPG/GIF, до 3 МБ)</label>
            <input class="input" type="file" name="avatar" accept="image/jpeg,image/gif">
          </div>
          <input class="button" type="submit" value="Загрузить">
        </form>
        <p class="mt8">Изображение будет обрезано и уменьшено до 200×200.</p>
      </div>
    </div>
  </div>

  <div class="col-right">
    <div class="card">
      <div class="card-h">Подсказки</div>
      <div class="card-b">
        <ul class="link-list">
          <li>Поддержка: JPG/GIF</li>
          <li>Размер &le; 3 МБ</li>
          <li>Квадратный кроп 200×200</li>
        </ul>
      </div>
    </div>
  </div>
</div>
