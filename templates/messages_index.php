<h2 style="margin-bottom: 10px">Ваши диалоги</h2>

<?php if (empty($dialogs)): ?>
    <p>Нет переписок.</p>
<?php else: ?>
    <table class="dialogs-table" cellpadding="6" cellspacing="0" border="0">

        <?php foreach ($dialogs as $dialog): ?>
            <div style="border:1px solid #ccc; padding:5px; margin:8px 0; overflow:hidden;">
                <!-- аватар + логин слева -->
                <div style="float:left; width:60px; text-align:center; margin-right:8px;">
                    <img src="<?= h($dialog['other_user_avatar'] ?: '/assets/img/default.jpg') ?>" width="40" height="40" alt="">
                    <a href="/profile/<?= h($dialog['other_user_id']) ?>" style="font-size:80%;"><?= h($dialog['other_user_login']) ?></a>
                </div>

                <!-- текст и дата -->
                <div style="margin-left:70px;">
                    <div style="font-size:90%; color:#555; text-align:right;">
                        <?= h($dialog['last_created_at']) ?>
                    </div>
                    <a href="/messages/<?= $dialog['other_user_id'] ?>"><div><?= nl2br(h($dialog['last_message'])) ?></div></a>
                </div>

                <div style="clear:both;"></div>
            </div>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
