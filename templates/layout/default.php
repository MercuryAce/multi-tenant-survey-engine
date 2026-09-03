<?php
/**
 * @var \App\View\AppView $this
 * @var array $navItems
 * @var \AdminKit\Model\Entity\Tenant|null $currentTenant
 * @var object|null $currentUser
 */
$brand = $currentTenant->brand_name ?? 'Engagement Platform';
$color = $currentTenant->primary_color ?? '#1f4e79';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($brand) ?> · <?= h($this->fetch('title') ?: 'Admin') ?></title>
    <?= $this->Html->meta('icon') ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <?= $this->Html->css('app') ?>
    <style>:root { --brand: <?= h($color) ?>; }</style>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="app-body">
<?php if ($currentUser): ?>
<nav class="sidebar">
    <div class="brand">
        <?php if (!empty($currentTenant->logo_path)): ?>
            <img src="<?= h($currentTenant->logo_path) ?>" alt="">
        <?php endif; ?>
        <span><?= h($brand) ?></span>
    </div>
    <ul class="nav flex-column">
        <?php foreach ($navItems as $item): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= h($item['url']) ?>"><?= h($item['label']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="sidebar-footer">
        <div class="small text-white-50"><?= h($currentUser->email ?? '') ?></div>
        <a class="btn btn-sm btn-outline-light mt-2" href="/logout">Log out</a>
    </div>
</nav>
<?php endif; ?>
<main class="main-content <?= $currentUser ? '' : 'full' ?>">
    <?= $this->Flash->render() ?>
    <?= $this->fetch('content') ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?= $this->fetch('scriptBottom') ?>
</body>
</html>
