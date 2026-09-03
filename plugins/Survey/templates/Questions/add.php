<?php
/**
 * @var \App\View\AppView $this
 * @var \Survey\Model\Entity\Question $question
 */
$this->assign('title', 'Add question');
$natures = ['standard' => 'Standard', 'demonstrated' => 'Demonstrated', 'importance' => 'Importance', 'enps' => 'eNPS'];
$types = ['likert' => 'Likert', 'enps' => 'eNPS', 'text' => 'Free text', 'choice' => 'Choice'];
?>
<h1 class="h3">Add question</h1>
<?= $this->Form->create($question) ?>
<?= $this->Form->hidden('survey_id') ?>
<?= $this->Form->control('title', ['class' => 'form-control']) ?>
<?= $this->Form->control('body', ['class' => 'form-control']) ?>
<?= $this->Form->control('nature', ['class' => 'form-select', 'options' => $natures]) ?>
<?= $this->Form->control('type', ['class' => 'form-select', 'options' => $types]) ?>
<?= $this->Form->control('competency', ['class' => 'form-control']) ?>
<?= $this->Form->control('sort_order', ['class' => 'form-control', 'default' => 0]) ?>
<?= $this->Form->control('is_required') ?>
<?= $this->Form->control('is_filter', ['label' => 'Demographic / filter question']) ?>
<?= $this->Form->button('Save', ['class' => 'btn btn-primary mt-3']) ?>
<?= $this->Form->end() ?>
