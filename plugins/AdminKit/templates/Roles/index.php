<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $roles
 */
$this->assign('title', 'Roles');
?>
<h1 class="h3">Roles</h1>
<table class="table">
    <thead><tr><th>Name</th><th>Slug</th></tr></thead>
    <tbody>
    <?php foreach ($roles as $role): ?>
        <tr><td><?= h($role->name) ?></td><td><?= h($role->slug) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
