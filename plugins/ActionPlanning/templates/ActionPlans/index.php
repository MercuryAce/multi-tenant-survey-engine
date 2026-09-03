<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $plans
 */
$this->assign('title', 'Action plans');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Action plans</h1>
    <a class="btn btn-primary" href="/action-plans/add">New plan</a>
</div>
<table class="table">
    <thead><tr><th>Plan</th><th>Survey</th><th>Status</th><th>Progress</th></tr></thead>
    <tbody>
    <?php foreach ($plans as $plan): ?>
        <?php
        $total = count($plan->action_plan_tasks);
        $done = 0;
        foreach ($plan->action_plan_tasks as $task) {
            if ($task->completed_date) {
                $done++;
            }
        }
        ?>
        <tr>
            <td><?= $this->Html->link($plan->name, ['action' => 'view', $plan->id]) ?></td>
            <td><?= h($plan->survey->title ?? '—') ?></td>
            <td><?= h($plan->status) ?></td>
            <td><?= $done ?> / <?= $total ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
