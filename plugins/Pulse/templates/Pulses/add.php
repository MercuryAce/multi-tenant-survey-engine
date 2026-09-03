<?php
/**
 * @var \App\View\AppView $this
 * @var \Survey\Model\Entity\Survey $pulse
 */
$this->assign('title', 'New pulse');
?>
<h1 class="h3">New pulse survey</h1>
<p class="text-muted">A short weekly or monthly check-in. Three starter questions are added automatically.</p>
<?= $this->Form->create($pulse) ?>
<?= $this->Form->hidden('kind', ['value' => 'pulse']) ?>
<?= $this->Form->control('title', ['class' => 'form-control']) ?>
<?= $this->Form->control('organisation_id', ['class' => 'form-select']) ?>
<?= $this->Form->control('welcome_text', ['class' => 'form-control', 'type' => 'textarea']) ?>
<?= $this->Form->control('min_responses', ['class' => 'form-control', 'default' => 4]) ?>
<?= $this->Form->control('status', ['class' => 'form-select', 'options' => ['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed'], 'default' => 'open']) ?>
<?= $this->Form->button('Create', ['class' => 'btn btn-primary mt-3']) ?>
<?= $this->Form->end() ?>
