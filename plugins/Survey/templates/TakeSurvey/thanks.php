<?php
/**
 * @var \App\View\AppView $this
 * @var \Survey\Model\Entity\Survey $survey
 */
$this->assign('title', 'Thank you');
?>
<div class="survey-panel card shadow-sm text-center">
    <h1 class="h3 mb-3">Thank you</h1>
    <p class="lead mb-0"><?= nl2br(h($survey->thankyou_text ?: 'Your responses have been recorded.')) ?></p>
</div>
