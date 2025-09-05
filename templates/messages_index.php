<h2 style="margin-bottom: 10px">Ваши диалоги</h2>

<?php if (empty($dialogs)): ?>
    <p>Нет переписок.</p>
<?php else: ?>
    <table class="dialogs-table" cellpadding="6" cellspacing="0" border="0">
        <?php foreach ($dialogs as $dialog): ?>
            <tr>
                <td width="50%">
                    <a href="/messages/<?= $dialog['other_user_id'] ?>">
                        <b><?= h($dialog['other_user_login']) ?></b>
                    </a>
                </td>
                <td width="40%">
                    <?= h(mb_strimwidth($dialog['last_message'], 0, 50, '...')) ?>
                </td>
                <td width="10%" align="right">
                    <span class="date"><?= h($dialog['last_created_at']) ?></span>
                    <?php if (!$dialog['last_read'] && $dialog['last_receiver_id'] === current_user_id()): ?>
                        <span class="badge">новое</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
