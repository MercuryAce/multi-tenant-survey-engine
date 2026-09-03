<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $cycles
 */
$this->assign('title', '360 feedback');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">360 feedback</h1>
    <a class="btn btn-primary" href="/feedback-cycles/add">New cycle</a>
</div>
<table class="table">
    <thead><tr><th>Cycle</th><th>Survey</th><th>Status</th><th>Assignments</th></tr></thead>
    <tbody>
    <?php foreach ($cycles as $cycle): ?>
        <tr>
            <td><?= $this->Html->link($cycle->name, ['action' => 'view', $cycle->id]) ?></td>
            <td><?= h($cycle->survey->title ?? '') ?></td>
            <td><?= h($cycle->status) ?></td>
            <td><?= count($cycle->rater_assignments) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
