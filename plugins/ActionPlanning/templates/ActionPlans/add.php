<?php
/**
 * @var \App\View\AppView $this
 * @var \ActionPlanning\Model\Entity\ActionPlan $plan
 */
$this->assign('title', 'New action plan');
?>
<h1 class="h3">New action plan</h1>
<?= $this->Form->create($plan) ?>
<?= $this->Form->control('name', ['class' => 'form-control']) ?>
<?= $this->Form->control('details', ['class' => 'form-control', 'type' => 'textarea', 'rows' => 4]) ?>
<?= $this->Form->control('survey_id', ['class' => 'form-select', 'empty' => '—', 'label' => 'Linked survey']) ?>
<?= $this->Form->control('user_id', ['class' => 'form-select', 'empty' => '—', 'label' => 'Owner']) ?>
<?= $this->Form->control('status', ['class' => 'form-select', 'options' => ['open' => 'Open', 'paused' => 'Paused', 'done' => 'Done']]) ?>
<?= $this->Form->control('start_date', ['class' => 'form-control', 'type' => 'date']) ?>
<?= $this->Form->control('end_date', ['class' => 'form-control', 'type' => 'date']) ?>
<?= $this->Form->button('Save', ['class' => 'btn btn-primary mt-3']) ?>
<?= $this->Form->end() ?>
