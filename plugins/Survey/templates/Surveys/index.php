<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $surveys
 */
$this->assign('title', 'Surveys');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Surveys</h1>
    <a class="btn btn-primary" href="/surveys/add">New survey</a>
</div>
<table class="table">
    <thead><tr><th>Title</th><th>Kind</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($surveys as $survey): ?>
        <tr>
            <td><?= $this->Html->link($survey->title, ['action' => 'view', $survey->id]) ?></td>
            <td><?= h($survey->kind) ?></td>
            <td><?= h($survey->status) ?></td>
            <td><?= $this->Html->link('Edit', ['action' => 'edit', $survey->id]) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
