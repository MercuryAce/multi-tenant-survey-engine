<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $pulses
 */
$this->assign('title', 'Pulse');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Pulse surveys</h1>
    <a class="btn btn-primary" href="/pulses/add">New pulse</a>
</div>
<p class="text-muted">Short-cycle check-ins. Same scoring engine as engagement surveys; kind is <code>pulse</code>.</p>
<table class="table">
    <thead><tr><th>Title</th><th>Status</th><th>Questions</th><th>Invites</th><th>Responses</th></tr></thead>
    <tbody>
    <?php foreach ($pulses as $pulse): ?>
        <tr>
            <td><?= $this->Html->link($pulse->title, ['action' => 'view', $pulse->id]) ?></td>
            <td><?= h($pulse->status) ?></td>
            <td><?= count($pulse->questions) ?></td>
            <td><?= count($pulse->invitations) ?></td>
            <td><?= count($pulse->responses) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
