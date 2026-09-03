<?php
/**
 * @var \App\View\AppView $this
 * @var \Survey\Model\Entity\Survey $survey
 * @var \Survey\Model\Entity\Invitation $invitation
 * @var \Survey\Model\Entity\Question|null $question
 * @var int $step
 * @var int $total
 * @var int $progress
 * @var array $draft
 */
$this->assign('title', $survey->title);
$answer = $question ? ($draft['answers'][(string)$question->id] ?? '') : '';
?>
<div class="survey-panel card shadow-sm">
    <?php if ($total > 0): ?>
        <div class="survey-progress">
            <div class="d-flex justify-content-between small text-muted mb-1">
                <span><?= $step === 0 ? 'Welcome' : 'Question ' . (int)$step . ' of ' . (int)$total ?></span>
                <span><?= (int)$progress ?>%</span>
            </div>
            <div class="progress" role="progressbar" aria-valuenow="<?= (int)$progress ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: <?= (int)$progress ?>%"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($step === 0): ?>
        <h1 class="h3 mb-3"><?= h($survey->title) ?></h1>
        <?php if ($survey->welcome_text): ?>
            <p class="lead"><?= nl2br(h($survey->welcome_text)) ?></p>
        <?php else: ?>
            <p class="lead">Your answers are confidential. Please answer as honestly as you can.</p>
        <?php endif; ?>
        <?php if ($total === 0): ?>
            <p class="text-muted">This survey has no questions yet.</p>
        <?php else: ?>
            <p class="text-muted mb-4"><?= (int)$total ?> question<?= $total === 1 ? '' : 's' ?> · about <?= max(1, (int)ceil($total * 0.5)) ?> minutes</p>
            <a class="btn btn-primary btn-lg" href="/take/<?= h($invitation->token) ?>?step=1">Start</a>
        <?php endif; ?>
    <?php elseif ($question): ?>
        <?php if ($question->is_filter): ?>
            <div class="badge text-bg-light text-muted mb-2">About you</div>
        <?php elseif ($question->competency): ?>
            <div class="badge text-bg-light text-muted mb-2"><?= h($question->competency) ?></div>
        <?php endif; ?>
        <h1 class="h4 mb-3"><?= h($question->title) ?></h1>
        <?php if ($question->body): ?>
            <p class="text-muted"><?= nl2br(h($question->body)) ?></p>
        <?php endif; ?>

        <?= $this->Form->create(null, ['url' => '/take/' . $invitation->token . '/step']) ?>
        <?= $this->Form->hidden('step', ['value' => $step]) ?>

        <?php if (in_array($question->type, ['likert', 'enps', 'choice'], true) && $question->question_options): ?>
            <div class="scale-grid <?= count($question->question_options) > 7 ? 'scale-grid-wide' : '' ?>" role="radiogroup" aria-label="<?= h($question->title) ?>">
                <?php foreach ($question->question_options as $opt): ?>
                    <?php $id = 'q' . (int)$question->id . 'o' . (int)$opt->id; ?>
                    <input class="btn-check" type="radio" name="answer" value="<?= h((string)$opt->value) ?>" id="<?= $id ?>" <?= (string)$answer === (string)$opt->value ? 'checked' : '' ?> <?= $question->is_required ? 'required' : '' ?>>
                    <label class="scale-option" for="<?= $id ?>">
                        <span class="scale-value"><?= h((string)($opt->value ?? $opt->label)) ?></span>
                        <?php if ((string)$opt->label !== (string)$opt->value): ?>
                            <span class="scale-label"><?= h($opt->label) ?></span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (in_array($question->type, ['likert', 'enps'], true)): ?>
                <div class="d-flex justify-content-between small text-muted mt-2">
                    <span>Strongly disagree</span>
                    <span>Strongly agree</span>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <textarea class="form-control" name="answer" rows="4" <?= $question->is_required ? 'required' : '' ?>><?= h((string)$answer) ?></textarea>
        <?php endif; ?>

        <div class="d-flex justify-content-between mt-4">
            <a class="btn btn-outline-secondary" href="/take/<?= h($invitation->token) ?>?step=<?= max(0, $step - 1) ?>">Back</a>
            <?= $this->Form->button($step >= $total ? 'Submit' : 'Next', ['class' => 'btn btn-primary']) ?>
        </div>
        <?= $this->Form->end() ?>
    <?php endif; ?>
</div>
