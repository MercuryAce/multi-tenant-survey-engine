<?php
/**
 * Print / export shell — no chrome.
 * @var \App\View\AppView $this
 */
$brand = $currentTenant->brand_name ?? 'Engagement Platform';
$color = $currentTenant->primary_color ?? '#1f4e79';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <title><?= h($this->fetch('title') ?: $brand) ?></title>
    <style>
        body { font-family: Georgia, serif; color: #222; margin: 2rem; }
        h1, h2 { color: <?= h($color) ?>; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: .4rem .6rem; text-align: left; }
        .muted { color: #666; font-size: .9rem; }
    </style>
</head>
<body>
    <p class="muted"><?= h($brand) ?></p>
    <?= $this->fetch('content') ?>
</body>
</html>
