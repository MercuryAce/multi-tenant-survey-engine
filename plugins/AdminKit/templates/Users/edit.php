<?php
/**
 * @var \App\View\AppView $this
 * @var \AdminKit\Model\Entity\User $user
 */
$this->assign('title', $user->isNew() ? 'Add user' : 'Edit user');
?>
<h1 class="h3"><?= $user->isNew() ? 'Add user' : 'Edit user' ?></h1>
<?= $this->Form->create($user) ?>
<?= $this->Form->control('email', ['class' => 'form-control']) ?>
<?= $this->Form->control('password', ['class' => 'form-control', 'value' => '', 'required' => $user->isNew()]) ?>
<?= $this->Form->control('first_name', ['class' => 'form-control']) ?>
<?= $this->Form->control('last_name', ['class' => 'form-control']) ?>
<?= $this->Form->control('is_active') ?>
<div class="mt-3"><?= $this->Form->button('Save', ['class' => 'btn btn-primary']) ?></div>
<?= $this->Form->end() ?>
