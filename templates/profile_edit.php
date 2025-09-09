
<?php
$uid = current_user_id();
$u = profile_find($uid);
$errors = isset($errors) && is_array($errors) ? $errors : [];
?>
<h2>Редактирование профиля</h2>

<div class="cols">
    <div class="col-left">
        <div class="card">
            <div class="card-h">Меню</div>
            <div class="card-b">
                <ul class="tab-nav">
                    <li><a href="/profile">Мой профиль</a></li>
                    <li><a href="/settings">Аватар</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-center">
        <div class="card">
            <div class="card-h">Персональная информация</div>
            <div class="card-b">
                <?php if ($errors): ?>
                    <div style="border:1px solid #c77;background:#fee;padding:6px;margin-bottom:8px;">
                        <b>Исправьте ошибки:</b>
                        <ul style="margin:6px 0 0 16px;">
                            <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="/profile/edit">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                    <div class="form-row">
                        <label class="label">Имя (можете указать реально имя)*</label>
                        <input class="input" type="text" name="name" maxlength="100" value="<?= h($u['name'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <label class="label">Пол</label>
                        <select class="input" name="gender">
                            <option value="">— не указан —</option>
                            <option value="male"   <?= isset($u['gender']) && $u['gender']==='male'?'selected':'' ?>>Мужской</option>
                            <option value="female" <?= isset($u['gender']) && $u['gender']==='female'?'selected':'' ?>>Женский</option>
                            <option value="other"  <?= isset($u['gender']) && $u['gender']==='other'?'selected':'' ?>>Другое</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label class="label">Дата рождения (ГГГГ-ММ-ДД)</label>
                        <input class="input" type="text" name="birthdate" placeholder="1990-01-31"
                               value="<?= h($u['birthdate'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <label class="label">Город</label>
                        <input class="input" type="text" name="location" maxlength="120"
                               value="<?= h($u['location'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <label class="label">Сайт</label>
                        <input class="input" type="text" name="website" maxlength="190"
                               value="<?= h($u['website'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <label class="label">О себе</label>
                        <textarea class="input" name="bio" rows="4" style="height:80px;"><?= h($u['bio'] ?? '') ?></textarea>
                    </div>

                    <input class="button" type="submit" value="Сохранить">
                    <a class="button" href="/user/<?= $uid ?>" style="margin-left:6px;">Отмена</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-right">
        <div class="card">
            <div class="card-h">Подсказки</div>
            <div class="card-b">
                <ul class="link-list">
                    <li>Имя обязательно</li>
                    <li>Дата: ГГГГ-ММ-ДД</li>
                    <li>Сайт — полный URL (http/https)</li>
                    <li>«О себе» — до 255 символов</li>
                </ul>
            </div>
        </div>
    </div>
</div>