<?php
/**
 * @var \App\View\AppView $this
 */
$brand = $currentTenant->brand_name ?? 'Engagement Platform';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($brand) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <?= $this->Html->css('app') ?>
</head>
<body class="login-body">
<main class="login-card card shadow">
    <?= $this->Flash->render() ?>
    <?= $this->fetch('content') ?>
</main>
</body>
</html>
