<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Sign in');
?>
<h1 class="h4 mb-3">Sign in</h1>
<?= $this->Form->create(null) ?>
<div class="mb-3">
    <?= $this->Form->control('email', ['label' => 'Email', 'class' => 'form-control', 'required' => true]) ?>
</div>
<div class="mb-3">
    <?= $this->Form->control('password', ['label' => 'Password', 'class' => 'form-control', 'required' => true]) ?>
</div>
<?= $this->Form->button('Sign in', ['class' => 'btn btn-primary w-100']) ?>
<?= $this->Form->end() ?>
<p class="mt-3 mb-0 small text-muted">New organisation? <a href="/tenants/onboard">Create a tenant</a></p>
