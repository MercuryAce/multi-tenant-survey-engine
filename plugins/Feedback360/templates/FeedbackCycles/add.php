<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback360\Model\Entity\FeedbackCycle $cycle
 * @var iterable $surveys
 */
$this->assign('title', 'New 360 cycle');
?>
<h1 class="h3">New 360 cycle</h1>
<p class="text-muted">Uses a survey with kind “360”. Create that survey first if the list is empty.</p>
<?php if (count($surveys) === 0): ?>
    <div class="alert alert-info">
        No 360 surveys yet.
        <?= $this->Form->postLink('Create a starter 360 survey', ['action' => 'starterSurvey'], ['class' => 'alert-link']) ?>
    </div>
<?php endif; ?>
<?= $this->Form->create($cycle) ?>
<?= $this->Form->control('name', ['class' => 'form-control']) ?>
<?= $this->Form->control('survey_id', ['class' => 'form-select', 'options' => $surveys]) ?>
<?= $this->Form->control('status', ['class' => 'form-select', 'options' => ['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed']]) ?>
<?= $this->Form->button('Save', ['class' => 'btn btn-primary mt-3']) ?>
<?= $this->Form->end() ?>
