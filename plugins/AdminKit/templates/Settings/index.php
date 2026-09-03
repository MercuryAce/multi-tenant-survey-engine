<?php
/**
 * @var \App\View\AppView $this
 * @var \AdminKit\Model\Entity\Tenant $tenant
 */
$this->assign('title', 'Settings');
?>
<h1 class="h3">Branding and settings</h1>
<p class="text-muted">These values replace the old hardcoded company name, support email, and colours.</p>
<?= $this->Form->create($tenant) ?>
<?= $this->Form->control('brand_name', ['class' => 'form-control']) ?>
<?= $this->Form->control('support_email', ['class' => 'form-control']) ?>
<?= $this->Form->control('primary_color', ['class' => 'form-control', 'type' => 'color']) ?>
<?= $this->Form->control('logo_path', ['class' => 'form-control', 'label' => 'Logo URL']) ?>
<div class="mt-3"><?= $this->Form->button('Save', ['class' => 'btn btn-primary']) ?></div>
<?= $this->Form->end() ?>
