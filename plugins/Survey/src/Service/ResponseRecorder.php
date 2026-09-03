<?php
declare(strict_types=1);

namespace Survey\Service;

use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use RuntimeException;

class ResponseRecorder
{
    use LocatorAwareTrait;

    /**
     * @param array<int|string, mixed> $answersByQuestionId
     */
    public function record(object $invitation, array $answersByQuestionId): object
    {
        if ($invitation->used) {
            throw new RuntimeException('This invitation has already been used.');
        }

        $survey = $this->fetchTable('Survey.Surveys')
            ->find()
            ->applyOptions(['skipTenant' => true])
            ->contain(['Questions'])
            ->where(['Surveys.id' => $invitation->survey_id])
            ->firstOrFail();

        $responses = $this->fetchTable('Survey.Responses');
        $response = $responses->newEntity([
            'tenant_id' => $invitation->tenant_id,
            'survey_id' => $survey->id,
            'invitation_id' => $invitation->id,
            'organisation_node_id' => $invitation->organisation_node_id,
            'user_id' => $survey->is_anonymous ? null : $invitation->user_id,
            'submitted' => DateTime::now(),
        ]);
        $responses->saveOrFail($response);

        $answersTable = $this->fetchTable('Survey.Answers');
        foreach ($survey->questions as $question) {
            $raw = $answersByQuestionId[$question->id] ?? $answersByQuestionId[(string)$question->id] ?? null;
            $answersTable->save($answersTable->newEntity([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'numeric_value' => is_numeric($raw) ? (int)$raw : null,
                'text_value' => is_numeric($raw) || $raw === null || $raw === '' ? null : (string)$raw,
            ]));
        }

        $invitation->used = true;
        $this->fetchTable('Survey.Invitations')->saveOrFail($invitation);

        return $response;
    }
}
