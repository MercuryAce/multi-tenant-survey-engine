<?php
/**
 * @var \App\View\AppView $this
 * @var string $html
 * @var \Reporting\Model\Entity\ReportTemplate $template
 */
$this->assign('title', $template->name);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="/reports">Back to reports</a>
    <div class="btn-group">
        <a class="btn btn-outline-primary btn-sm" href="/reports/pdf/<?= (int)$template->id ?>">PDF</a>
        <a class="btn btn-outline-primary btn-sm" href="/reports/docx/<?= (int)$template->id ?>">Word</a>
        <a class="btn btn-outline-primary btn-sm" href="/reports/pptx/<?= (int)$template->id ?>">PowerPoint</a>
    </div>
</div>
<div class="card p-3"><?= $html ?></div>
