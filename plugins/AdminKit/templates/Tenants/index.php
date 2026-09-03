<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $tenants
 */
$this->assign('title', 'Tenants');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Tenants</h1>
    <a class="btn btn-primary" href="/tenants/onboard">Onboard</a>
</div>
<table class="table">
    <thead><tr><th>Name</th><th>Slug</th><th>Brand</th><th>Support</th></tr></thead>
    <tbody>
    <?php foreach ($tenants as $tenant): ?>
        <tr>
            <td><?= h($tenant->name) ?></td>
            <td><?= h($tenant->slug) ?></td>
            <td><?= h($tenant->brand_name) ?></td>
            <td><?= h($tenant->support_email) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
