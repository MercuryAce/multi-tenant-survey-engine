<?php
/**
 * @var \App\View\AppView $this
 * @var \Organisation\Model\Entity\OrganisationNode $organisationNode
 */
$orgs = $this->get('organisations') ?? [];
?>
<h1 class="h3"><?= $organisationNode->isNew() ? 'Add node' : 'Edit node' ?></h1>
<?= $this->Form->create($organisationNode) ?>
<?= $this->Form->control('organisation_id', ['class' => 'form-select']) ?>
<?= $this->Form->control('parent_id', ['class' => 'form-select', 'empty' => 'Root']) ?>
<?= $this->Form->control('name', ['class' => 'form-control']) ?>
<?= $this->Form->control('quota', ['class' => 'form-control']) ?>
<?= $this->Form->button('Save', ['class' => 'btn btn-primary mt-3']) ?>
<?= $this->Form->end() ?>
