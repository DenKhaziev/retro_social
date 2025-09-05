
<h2>Диалог с <?= h($otherUser['login']) ?></h2>

<div class="card">
    <div class="card-h">Сообщения</div>
    <div class="card-b">
        <?php if (empty($messages)): ?>
            <p>Сообщений пока нет.</p>
        <?php else: ?>
            <ul class="message-thread">
                <?php foreach ($messages as $msg): ?>
                    <li class="<?= $msg['sender_id'] === current_user_id() ? 'me' : 'them' ?>">
                        <div class="msg-body"><?= nl2br(h($msg['body'])) ?></div>
                        <div class="msg-meta"><?= h($msg['created_at']) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-h">Новое сообщение</div>
    <div class="card-b">
        <form method="post" action="/messages/send">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="receiver_id" value="<?= (int)$otherUser['id'] ?>">
            <div class="form-row">
                <label class="label">Текст сообщения</label>
                <textarea class="input" name="body" rows="4" required></textarea>
            </div>
            <input class="button" type="submit" value="Отправить">
        </form>
    </div>
</div>
