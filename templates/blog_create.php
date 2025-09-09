<h1>Новый пост</h1>

<?php if (!empty($error)): ?>
    <div style="color:red;"><?= h($error) ?></div>
<?php endif; ?>

<form method="post" action="/blog/create">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

    <div style="margin:8px 0;">
        <label>Заголовок:<br>
            <input type="text" name="title" style="width:300px;">
        </label>
    </div>

    <div style="margin:8px 0;">
        <label>Текст:<br>
            <textarea name="body" rows="10" cols="60"></textarea>
        </label>
    </div>

    <div style="margin:8px 0;">
        <label><input type="checkbox" name="is_draft" value="1"> Черновик</label><br>
        <label>Видимость:
            <select name="visibility">
                <option value="0">Публично</option>
                <option value="1">Только друзья</option>
                <option value="2">Приватно</option>
            </select>
        </label>
    </div>

    <div style="margin:8px 0;">
        <input type="submit" value="Опубликовать">
    </div>
</form>

<p><a href="/blog/<?= current_user_id() ?>">← Назад к блогу</a></p>
<?php
