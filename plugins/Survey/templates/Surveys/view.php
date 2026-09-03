<?php
/**
 * @var \App\View\AppView $this
 * @var \Survey\Model\Entity\Survey $survey
 */
$this->assign('title', $survey->title);
?>
<div class="d-flex justify-content-between page-title">
    <div>
        <h1 class="h3 mb-0"><?= h($survey->title) ?></h1>
        <div class="text-muted"><?= h($survey->kind) ?> · <?= h($survey->status) ?> · min responses <?= (int)$survey->min_responses ?></div>
    </div>
    <div class="btn-group">
        <a class="btn btn-outline-primary" href="/questions/add?survey_id=<?= (int)$survey->id ?>">Add question</a>
        <a class="btn btn-outline-primary" href="/invitations/add?survey_id=<?= (int)$survey->id ?>">Invite</a>
        <a class="btn btn-outline-primary" href="/invitations?survey_id=<?= (int)$survey->id ?>">Invitations</a>
        <a class="btn btn-primary" href="/analytics?survey_id=<?= (int)$survey->id ?>">Analytics</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted text-uppercase small">Questions</div>
            <div class="h3 mb-0"><?= count($survey->questions) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted text-uppercase small">Invitations</div>
            <div class="h3 mb-0"><?= count($survey->invitations) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted text-uppercase small">Responses</div>
            <div class="h3 mb-0"><?= count($survey->responses) ?></div>
        </div>
    </div>
</div>
<h2 class="h5">Questions</h2>
<table class="table">
    <thead><tr><th>#</th><th>Title</th><th>Nature</th><th>Type</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($survey->questions as $q): ?>
        <tr>
            <td><?= (int)$q->sort_order ?></td>
            <td>
                <?= h($q->title) ?>
                <?php if ($q->is_filter): ?><span class="badge text-bg-light">Demographic</span><?php endif; ?>
            </td>
            <td><?= h($q->nature) ?></td>
            <td><?= h($q->type) ?></td>
            <td><?= h($q->competency) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<h2 class="h5 mt-4">Recent invitations</h2>
<table class="table">
    <thead><tr><th>Email</th><th>Link</th><th>Emailed</th><th>Used</th></tr></thead>
    <tbody>
    <?php $shown = 0; foreach ($survey->invitations as $invite): if ($shown++ >= 8) { break; } ?>
        <tr>
            <td><?= h($invite->email) ?></td>
            <td><a href="/take/<?= h($invite->token) ?>">Open</a></td>
            <td><?= $invite->sent_at ? 'Yes' : 'No' ?></td>
            <td><?= $invite->used ? 'Yes' : 'No' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<p><a href="/invitations?survey_id=<?= (int)$survey->id ?>">View all invitations</a></p>
