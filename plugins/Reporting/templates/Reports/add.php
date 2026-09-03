<?php
/**
 * @var \App\View\AppView $this
 * @var \Reporting\Model\Entity\ReportTemplate $template
 */
$this->assign('title', 'New report template');
?>
<h1 class="h3">New report template</h1>
<?= $this->Form->create($template) ?>
<?= $this->Form->control('name', ['class' => 'form-control']) ?>
<?= $this->Form->control('survey_id', ['class' => 'form-select', 'empty' => '—']) ?>
<?= $this->Form->button('Create', ['class' => 'btn btn-primary mt-3']) ?>
<?= $this->Form->end() ?>
