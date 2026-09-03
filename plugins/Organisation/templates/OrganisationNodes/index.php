<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $nodes
 */
$this->assign('title', 'Organisation tree');

$nodes = $nodes ?? [];
$render = function ($items, $level = 0) use (&$render): string {
    $html = '<ul class="list-unstyled ms-' . min($level * 2, 5) . '">';
    foreach ($items as $node) {
        $html .= '<li class="mb-1">';
        $html .= str_repeat('— ', $level) . h($node->name);
        $html .= ' <span class="text-muted small">quota ' . (int)$node->quota . '</span> ';
        $html .= '<a href="/organisation-nodes/edit/' . (int)$node->id . '">edit</a>';
        if (!empty($node->children)) {
            $html .= $render($node->children, $level + 1);
        }
        $html .= '</li>';
    }

    return $html . '</ul>';
};
?>
<div class="d-flex justify-content-between page-title">
    <h1 class="h3">Organisation tree</h1>
    <a class="btn btn-primary" href="/organisation-nodes/add">Add node</a>
</div>
<div class="card stat-card p-3">
    <?php if ($nodes): ?>
        <?= $render($nodes) ?>
    <?php else: ?>
        <p class="text-muted">No nodes found</p>
    <?php endif; ?>
</div>
