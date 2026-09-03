<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback360\Model\Entity\FeedbackCycle $cycle
 * @var iterable $users
 * @var array $summary
 */
$this->assign('title', $cycle->name);
?>
<div class="d-flex justify-content-between page-title">
    <div>
        <h1 class="h3 mb-0"><?= h($cycle->name) ?></h1>
        <div class="text-muted">
            <?= h($cycle->survey->title ?? '') ?> · <?= h($cycle->status) ?>
            · <?= (int)$summary['completed'] ?> / <?= (int)$summary['assignments'] ?> complete
        </div>
    </div>
    <div class="btn-group">
        <?php if ($cycle->status !== 'open'): ?>
            <?= $this->Form->postLink('Open & send invites', ['action' => 'open', $cycle->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if ($cycle->status === 'open'): ?>
            <?= $this->Form->postLink('Close cycle', ['action' => 'close', $cycle->id], ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
        <?php if (!empty($cycle->survey_id)): ?>
            <a class="btn btn-outline-primary" href="/analytics?survey_id=<?= (int)$cycle->survey_id ?>">Survey analytics</a>
        <?php endif; ?>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card stat-card p-3 h-100">
            <h2 class="h6 text-uppercase text-muted">By relationship</h2>
            <?php if (!$summary['by_relationship']): ?>
                <p class="mb-0 text-muted">No completed ratings yet.</p>
            <?php else: ?>
                <table class="table table-sm mb-0">
                    <?php foreach ($summary['by_relationship'] as $rel => $row): ?>
                        <tr>
                            <td><?= h($rel) ?></td>
                            <td><?= $row['average'] !== null ? h((string)$row['average']) : '—' ?></td>
                            <td class="text-muted"><?= (int)$row['n'] ?> ratings</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stat-card p-3 h-100">
            <h2 class="h6 text-uppercase text-muted">By subject</h2>
            <?php if (!$summary['subjects']): ?>
                <p class="mb-0 text-muted">Assign raters to see subject scores.</p>
            <?php else: ?>
                <table class="table table-sm mb-0">
                    <?php foreach ($summary['subjects'] as $row): ?>
                        <tr>
                            <td><?= h($row['name']) ?></td>
                            <td><?= $row['average'] !== null ? h((string)$row['average']) : '—' ?></td>
                            <td class="text-muted"><?= (int)$row['n'] ?> ratings</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="card p-3 mb-4">
    <h2 class="h5">Assign a rater</h2>
    <?= $this->Form->create(null) ?>
    <div class="row g-3">
        <div class="col-md-4"><?= $this->Form->control('subject_user_id', ['label' => 'Subject', 'options' => $users, 'class' => 'form-select']) ?></div>
        <div class="col-md-4"><?= $this->Form->control('rater_user_id', ['label' => 'Rater', 'options' => $users, 'class' => 'form-select']) ?></div>
        <div class="col-md-4"><?= $this->Form->control('relationship', ['class' => 'form-select', 'options' => ['manager' => 'Manager', 'peer' => 'Peer', 'direct-report' => 'Direct report', 'self' => 'Self']]) ?></div>
    </div>
    <?= $this->Form->button('Assign', ['class' => 'btn btn-primary mt-3']) ?>
    <?= $this->Form->end() ?>
</div>
<h2 class="h5">Assignments</h2>
<table class="table">
    <thead><tr><th>Subject</th><th>Rater</th><th>Relationship</th><th>Invite</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($cycle->rater_assignments as $row): ?>
        <tr>
            <td><?= h(trim(($row->subject->first_name ?? '') . ' ' . ($row->subject->last_name ?? '')) ?: '#' . $row->subject_user_id) ?></td>
            <td><?= h($row->rater->email ?? ('#' . $row->rater_user_id)) ?></td>
            <td><?= h($row->relationship) ?></td>
            <td>
                <?php if ($row->invitation): ?>
                    <a href="/take/<?= h($row->invitation->token) ?>">Open</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td>
                <?php if ($row->invitation?->used): ?>
                    Complete
                <?php elseif ($row->invitation?->sent_at): ?>
                    Invited
                <?php else: ?>
                    Pending
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
