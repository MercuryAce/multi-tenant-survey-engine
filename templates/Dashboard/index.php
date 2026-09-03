<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $surveys
 * @var int $responses
 * @var int $nodes
 */
$this->assign('title', 'Dashboard');
?>
<div class="d-flex justify-content-between align-items-center page-title">
    <h1 class="h3 mb-0">Dashboard</h1>
    <a class="btn btn-primary" href="/surveys/add">New survey</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted small">Surveys</div>
            <div class="fs-3 fw-semibold"><?= (int)$surveys->count() ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted small">Responses</div>
            <div class="fs-3 fw-semibold"><?= (int)$responses ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted small">Organisation nodes</div>
            <div class="fs-3 fw-semibold"><?= (int)$nodes ?></div>
        </div>
    </div>
</div>
<div class="card stat-card">
    <div class="card-body">
        <h2 class="h5">Recent surveys</h2>
        <table class="table">
            <thead><tr><th>Title</th><th>Kind</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($surveys as $survey): ?>
                <tr>
                    <td><?= $this->Html->link($survey->title, ['plugin' => 'Survey', 'controller' => 'Surveys', 'action' => 'view', $survey->id]) ?></td>
                    <td><?= h($survey->kind) ?></td>
                    <td><?= h($survey->status) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
