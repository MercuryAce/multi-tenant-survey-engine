<?php
/**
 * Respondent-facing survey layout.
 *
 * @var \App\View\AppView $this
 * @var \AdminKit\Model\Entity\Tenant|null $currentTenant
 */
$brand = $currentTenant->brand_name ?? 'Engagement Platform';
$color = $currentTenant->primary_color ?? '#1f4e79';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($this->fetch('title') ?: $brand) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <?= $this->Html->css('app') ?>
    <style>:root { --brand: <?= h($color) ?>; }</style>
</head>
<body class="survey-body">
    <header class="survey-topbar">
        <div class="container survey-wrap d-flex align-items-center justify-content-between">
            <strong><?= h($brand) ?></strong>
            <span class="small text-white-50">Confidential</span>
        </div>
    </header>
    <main class="container survey-wrap py-4">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>
</body>
</html>
