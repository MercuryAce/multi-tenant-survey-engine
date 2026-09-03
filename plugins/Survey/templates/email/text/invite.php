<?php
/**
 * @var \App\View\AppView $this
 * @var object $survey
 * @var string $url
 * @var string $brand
 */
?>
Hello,

<?= $brand ?> has invited you to complete <?= $survey->title ?>.

<?php if (!empty($survey->welcome_text)): ?>
<?= $survey->welcome_text ?>

<?php endif; ?>
Open the survey:
<?= $url ?>

This link is personal. Please do not share it.
