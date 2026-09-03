<?php
/**
 * @var \App\View\AppView $this
 */
if (!$this->Paginator->total()) {
    return;
}
?>
<nav class="d-flex justify-content-between align-items-center mt-3">
    <div class="small text-muted"><?= $this->Paginator->counter('Page {{page}} of {{pages}}') ?></div>
    <ul class="pagination mb-0">
        <?= $this->Paginator->prev('Previous') ?>
        <?= $this->Paginator->next('Next') ?>
    </ul>
</nav>
