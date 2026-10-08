<?php
$messages = getFlash();
if (empty($messages)) return;
?>
<div class="flash-messages" role="alert" aria-live="polite">
    <?php foreach ($messages as $msg): ?>
        <div class="alert alert--<?= e($msg['type']) ?>">
            <?= e($msg['message']) ?>
        </div>
    <?php endforeach; ?>
</div>
