<?php
/**
 * @var \App\View\AppView $this
 * @var \Organisation\Model\Entity\Organisation $organisation
 */
echo $this->Form->create($organisation);
echo $this->Form->control('name', ['class' => 'form-control']);
echo $this->Form->control('logo_path', ['class' => 'form-control']);
echo $this->Form->button('Save', ['class' => 'btn btn-primary mt-3']);
echo $this->Form->end();
