<?php
/**
 * @var \App\View\AppView $this
 * @var object $survey
 * @var object $tenant
 * @var string $url
 * @var string $brand
 */
?>
<p>Hello,</p>
<p><?= h($brand) ?> has invited you to complete <strong><?= h($survey->title) ?></strong>.</p>
<?php if (!empty($survey->welcome_text)): ?>
    <p><?= nl2br(h($survey->welcome_text)) ?></p>
<?php endif; ?>
<p><a href="<?= h($url) ?>">Open the survey</a></p>
<p>If the link does not work, copy this address into your browser:<br><?= h($url) ?></p>
<p>This link is personal. Please do not share it.</p>
