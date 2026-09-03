<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $invitations
 * @var \Survey\Model\Entity\Survey|null $survey
 * @var int $surveyId
 */
$this->assign('title', 'Invitations');
?>
<div class="d-flex justify-content-between page-title">
    <div>
        <h1 class="h3 mb-0">Invitations</h1>
        <?php if ($survey): ?>
            <div class="text-muted"><?= h($survey->title) ?></div>
        <?php endif; ?>
    </div>
    <div class="btn-group">
        <?php if ($surveyId): ?>
            <a class="btn btn-outline-secondary" href="/surveys/view/<?= (int)$surveyId ?>">Survey</a>
            <a class="btn btn-primary" href="/invitations/add?survey_id=<?= (int)$surveyId ?>">Invite</a>
        <?php endif; ?>
    </div>
</div>
<table class="table">
    <thead>
        <tr>
            <th>Email</th>
            <th>Unit</th>
            <th>Link</th>
            <th>Emailed</th>
            <th>Used</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($invitations as $invite): ?>
        <tr>
            <td><?= h($invite->email ?: '—') ?></td>
            <td><?= h($invite->organisation_node->name ?? '—') ?></td>
            <td>
                <a href="<?= h(\Cake\Routing\Router::url('/take/' . $invite->token, true)) ?>">
                    /take/<?= h(substr((string)$invite->token, 0, 8)) ?>…
                </a>
            </td>
            <td>
                <?php if ($invite->sent_at): ?>
                    <?= h($invite->sent_at) ?>
                    <?php if ((int)$invite->sent_count > 1): ?>
                        <span class="text-muted">(<?= (int)$invite->sent_count ?>)</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-muted">Not sent</span>
                <?php endif; ?>
            </td>
            <td><?= $invite->used ? 'Yes' : 'No' ?></td>
            <td>
                <?php if (!$invite->used && $invite->email): ?>
                    <?= $this->Form->postLink('Resend', ['action' => 'resend', $invite->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $this->element('paginator') ?>
