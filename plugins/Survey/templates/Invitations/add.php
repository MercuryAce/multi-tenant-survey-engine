<?php
/**
 * @var \App\View\AppView $this
 * @var int $survey_id
 * @var \Survey\Model\Entity\Survey|null $survey
 */
$this->assign('title', 'Invite');
?>
<div class="d-flex justify-content-between page-title">
    <div>
        <h1 class="h3 mb-0">Invite respondents</h1>
        <?php if ($survey): ?>
            <div class="text-muted"><?= h($survey->title) ?></div>
        <?php endif; ?>
    </div>
    <a class="btn btn-outline-secondary" href="/invitations?survey_id=<?= (int)$survey_id ?>">Invitation list</a>
</div>
<div class="card p-4" style="max-width: 640px">
    <?= $this->Form->create(null) ?>
    <?= $this->Form->hidden('survey_id', ['value' => $survey_id]) ?>
    <?= $this->Form->control('email', [
        'class' => 'form-control',
        'label' => 'Email',
        'type' => 'email',
        'placeholder' => 'one.person@example.com',
    ]) ?>
    <?= $this->Form->control('emails', [
        'class' => 'form-control',
        'type' => 'textarea',
        'rows' => 5,
        'label' => 'Or paste several addresses',
        'placeholder' => "one per line, or separated by commas",
    ]) ?>
    <?= $this->Form->control('organisation_node_id', [
        'class' => 'form-select',
        'empty' => 'Whole organisation',
        'label' => 'Organisation unit',
    ]) ?>
    <div class="form-check my-3">
        <?= $this->Form->checkbox('send_email', ['checked' => true, 'id' => 'send-email', 'value' => '1']) ?>
        <label class="form-check-label" for="send-email">Send invitation emails now</label>
    </div>
    <?= $this->Form->button('Create invitations', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
