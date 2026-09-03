<?php
/**
 * @var \App\View\AppView $this
 * @var \Survey\Model\Entity\Survey $survey
 */
$this->assign('title', 'New survey');
?>
<h1 class="h3">New survey</h1>
<?= $this->Form->create($survey) ?>
<?= $this->Form->control('title', ['class' => 'form-control']) ?>
<?= $this->Form->control('slug', ['class' => 'form-control']) ?>
<?= $this->Form->control('kind', ['class' => 'form-select', 'options' => ['engagement' => 'Engagement', 'pulse' => 'Pulse', '360' => '360']]) ?>
<?= $this->Form->control('status', ['class' => 'form-select', 'options' => ['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed']]) ?>
<?= $this->Form->control('organisation_id', ['class' => 'form-select']) ?>
<?= $this->Form->control('welcome_text', ['class' => 'form-control', 'rows' => 3]) ?>
<?= $this->Form->control('thankyou_text', ['class' => 'form-control', 'rows' => 2]) ?>
<div class="row">
    <div class="col"><?= $this->Form->control('scale_min', ['class' => 'form-control', 'default' => 1]) ?></div>
    <div class="col"><?= $this->Form->control('scale_max', ['class' => 'form-control', 'default' => 5]) ?></div>
    <div class="col"><?= $this->Form->control('positive_min', ['class' => 'form-control', 'default' => 4]) ?></div>
    <div class="col"><?= $this->Form->control('positive_max', ['class' => 'form-control', 'default' => 5]) ?></div>
    <div class="col"><?= $this->Form->control('min_responses', ['class' => 'form-control', 'default' => 4]) ?></div>
</div>
<?= $this->Form->control('is_anonymous') ?>
<?= $this->Form->button('Save', ['class' => 'btn btn-primary mt-3']) ?>
<?= $this->Form->end() ?>
