<?php
/**
 * @var \App\View\AppView $this
 * @var \Survey\Model\Entity\Survey $pulse
 */
$this->assign('title', $pulse->title);
?>
<div class="d-flex justify-content-between page-title">
    <div>
        <h1 class="h3 mb-0"><?= h($pulse->title) ?></h1>
        <div class="text-muted">Pulse · <?= h($pulse->status) ?> · floor <?= (int)$pulse->min_responses ?></div>
    </div>
    <div class="btn-group">
        <?php if ($pulse->status !== 'open'): ?>
            <?= $this->Form->postLink('Open pulse', ['action' => 'open', $pulse->id], ['class' => 'btn btn-outline-primary']) ?>
        <?php endif; ?>
        <a class="btn btn-outline-primary" href="/questions/add?survey_id=<?= (int)$pulse->id ?>">Add question</a>
        <a class="btn btn-primary" href="/analytics?survey_id=<?= (int)$pulse->id ?>">Analytics</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card stat-card p-3"><div class="text-muted small text-uppercase">Questions</div><div class="h3 mb-0"><?= count($pulse->questions) ?></div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="text-muted small text-uppercase">Invitations</div><div class="h3 mb-0"><?= count($pulse->invitations) ?></div></div></div>
    <div class="col-md-4"><div class="card stat-card p-3"><div class="text-muted small text-uppercase">Responses</div><div class="h3 mb-0"><?= count($pulse->responses) ?></div></div></div>
</div>
<div class="card p-3 mb-4">
    <h2 class="h5">Invite this pulse</h2>
    <?= $this->Form->create(null) ?>
    <?= $this->Form->control('email', ['class' => 'form-control', 'label' => 'Email']) ?>
    <?= $this->Form->control('emails', ['class' => 'form-control', 'type' => 'textarea', 'rows' => 3, 'label' => 'Or several addresses']) ?>
    <?= $this->Form->button('Send invitations', ['class' => 'btn btn-primary mt-3']) ?>
    <?= $this->Form->end() ?>
</div>
<h2 class="h5">Questions</h2>
<ul>
    <?php foreach ($pulse->questions as $q): ?>
        <li><?= h($q->title) ?> <span class="text-muted"><?= h($q->nature) ?></span></li>
    <?php endforeach; ?>
</ul>
<h2 class="h5 mt-4">Invitations</h2>
<table class="table">
    <?php foreach ($pulse->invitations as $invite): ?>
        <tr>
            <td><?= h($invite->email) ?></td>
            <td><a href="/take/<?= h($invite->token) ?>">Open</a></td>
            <td><?= $invite->used ? 'Used' : 'Open' ?></td>
        </tr>
    <?php endforeach; ?>
</table>
