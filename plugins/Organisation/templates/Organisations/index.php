<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $organisations
 */
$this->assign('title', 'Organisations');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Organisations</h1>
    <a class="btn btn-primary" href="/organisations/add">Add</a>
</div>
<table class="table">
    <?php foreach ($organisations as $org): ?>
        <tr><td><?= h($org->name) ?></td><td><?= $this->Html->link('Edit', ['action' => 'edit', $org->id]) ?></td></tr>
    <?php endforeach; ?>
</table>
