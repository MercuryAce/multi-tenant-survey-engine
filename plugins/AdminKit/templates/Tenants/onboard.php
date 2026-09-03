<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Create organisation');
?>
<h1 class="h4 mb-3">Create your organisation</h1>
<p class="text-muted">This sets up a tenant, an admin user, default roles, and a root organisation node.</p>
<?= $this->Form->create(null) ?>
<?= $this->Form->control('name', ['label' => 'Organisation name', 'class' => 'form-control', 'required' => true]) ?>
<?= $this->Form->control('slug', ['label' => 'URL slug', 'class' => 'form-control', 'required' => true]) ?>
<?= $this->Form->control('brand_name', ['label' => 'Brand name (shown in the UI)', 'class' => 'form-control']) ?>
<?= $this->Form->control('support_email', ['label' => 'Support email', 'class' => 'form-control', 'type' => 'email']) ?>
<?= $this->Form->control('primary_color', ['label' => 'Brand colour', 'class' => 'form-control', 'type' => 'color', 'value' => '#1f4e79']) ?>
<hr>
<?= $this->Form->control('admin_email', ['label' => 'Admin email', 'class' => 'form-control', 'required' => true]) ?>
<?= $this->Form->control('admin_password', ['label' => 'Admin password', 'type' => 'password', 'class' => 'form-control', 'required' => true]) ?>
<?= $this->Form->control('admin_first_name', ['label' => 'First name', 'class' => 'form-control']) ?>
<?= $this->Form->control('admin_last_name', ['label' => 'Last name', 'class' => 'form-control']) ?>
<div class="mt-3"><?= $this->Form->button('Create tenant', ['class' => 'btn btn-primary w-100']) ?></div>
<?= $this->Form->end() ?>
<p class="mt-3 small"><a href="/login">Back to sign in</a></p>
