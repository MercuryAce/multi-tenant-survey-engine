<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $templates
 */
$this->assign('title', 'Reports');
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Reports</h1>
    <a class="btn btn-primary" href="/reports/add">New template</a>
</div>
<table class="table">
    <thead><tr><th>Name</th><th>Survey</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($templates as $template): ?>
        <tr>
            <td><?= h($template->name) ?></td>
            <td><?= h($template->survey->title ?? '') ?></td>
            <td>
                <a href="/reports/preview/<?= (int)$template->id ?>">Preview</a>
                · <a href="/reports/pdf/<?= (int)$template->id ?>">PDF</a>
                · <a href="/reports/docx/<?= (int)$template->id ?>">Word</a>
                · <a href="/reports/pptx/<?= (int)$template->id ?>">PowerPoint</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
