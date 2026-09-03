<?php
/**
 * @var \App\View\AppView $this
 * @var \ActionPlanning\Model\Entity\ActionPlan $plan
 * @var array{total:int,done:int,percent:int} $progress
 */
$this->assign('title', $plan->name);
?>
<div class="d-flex justify-content-between page-title">
    <div>
        <h1 class="h3 mb-0"><?= h($plan->name) ?></h1>
        <div class="text-muted">
            <?= h($plan->status) ?>
            <?php if ($plan->survey): ?>
                · <a href="/surveys/view/<?= (int)$plan->survey_id ?>"><?= h($plan->survey->title) ?></a>
            <?php endif; ?>
            <?php if ($plan->user): ?>
                · <?= h($plan->user->first_name . ' ' . $plan->user->last_name) ?>
            <?php endif; ?>
        </div>
    </div>
    <?= $this->Form->create(null, ['url' => ['action' => 'status', $plan->id], 'class' => 'd-flex gap-2']) ?>
        <select name="status" class="form-select" onchange="this.form.submit()">
            <?php foreach (['open' => 'Open', 'paused' => 'Paused', 'done' => 'Done'] as $value => $label): ?>
                <option value="<?= h($value) ?>" <?= $plan->status === $value ? 'selected' : '' ?>><?= h($label) ?></option>
            <?php endforeach; ?>
        </select>
    <?= $this->Form->end() ?>
</div>
<p><?= nl2br(h((string)$plan->details)) ?></p>
<div class="mb-4">
    <div class="d-flex justify-content-between small text-muted mb-1">
        <span>Progress</span>
        <span><?= (int)$progress['done'] ?> / <?= (int)$progress['total'] ?></span>
    </div>
    <div class="progress" style="height:.5rem">
        <div class="progress-bar" style="width: <?= (int)$progress['percent'] ?>%"></div>
    </div>
</div>
<h2 class="h5">Tasks</h2>
<table class="table">
    <?php foreach ($plan->action_plan_tasks as $task): ?>
        <tr>
            <td><?= h($task->name) ?></td>
            <td><?= $task->completed_date ? 'Done ' . h((string)$task->completed_date) : 'Open' ?></td>
            <td>
                <?php if ($task->completed_date): ?>
                    <?= $this->Form->postLink('Reopen', ['action' => 'reopenTask', $task->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                <?php else: ?>
                    <?= $this->Form->postLink('Complete', ['action' => 'completeTask', $task->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="card p-3" style="max-width:480px">
    <?= $this->Form->create(null) ?>
    <?= $this->Form->control('name', ['label' => 'New task', 'class' => 'form-control']) ?>
    <?= $this->Form->button('Add task', ['class' => 'btn btn-primary mt-3']) ?>
    <?= $this->Form->end() ?>
</div>
