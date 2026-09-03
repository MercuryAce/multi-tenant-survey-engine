<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $users
 */
$this->assign('title', 'Users');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Users</h1>
    <a class="btn btn-primary" href="/users/add">Add user</a>
</div>
<table class="table">
    <thead><tr><th>Email</th><th>Name</th><th>Active</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= h($user->email) ?></td>
            <td><?= h($user->first_name . ' ' . $user->last_name) ?></td>
            <td><?= $user->is_active ? 'Yes' : 'No' ?></td>
            <td><?= $this->Html->link('Edit', ['action' => 'edit', $user->id]) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
