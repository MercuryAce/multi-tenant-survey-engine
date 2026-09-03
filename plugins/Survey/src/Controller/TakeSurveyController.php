<?php
declare(strict_types=1);

namespace Survey\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Survey\Service\ResponseRecorder;

class TakeSurveyController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['view', 'step', 'submit']);
        $this->FormProtection->setConfig('unlockedActions', ['step', 'submit']);
        $this->viewBuilder()->setLayout('survey');
    }

    public function view(string $token): ?Response
    {
        $invitation = $this->invitation($token);
        $survey = $this->survey($invitation);
        if ($invitation->used) {
            $this->set(compact('survey', 'invitation'));
            $this->render('thanks');

            return null;
        }

        $questions = collection($survey->questions)->toList();
        $total = count($questions);
        $step = (int)$this->request->getQuery('step', 0);
        $draft = $this->draft($token);
        if ($step < 0 || $step > $total) {
            $step = 0;
        }
        $question = $step > 0 ? ($questions[$step - 1] ?? null) : null;
        $progress = $total > 0 ? (int)round(100 * max(0, $step - 1) / $total) : 0;
        $this->set(compact('survey', 'invitation', 'questions', 'question', 'step', 'total', 'progress', 'draft'));

        return null;
    }

    public function step(string $token): ?Response
    {
        $this->request->allowMethod(['post']);
        $invitation = $this->invitation($token);
        if ($invitation->used) {
            return $this->redirect('/take/' . $token);
        }
        $survey = $this->survey($invitation);
        $questions = collection($survey->questions)->toList();
        $total = count($questions);
        $step = (int)$this->request->getData('step', 1);
        $question = $questions[$step - 1] ?? null;
        if (!$question) {
            return $this->redirect('/take/' . $token);
        }

        $raw = $this->request->getData('answer');
        if ($question->is_required && ($raw === null || $raw === '')) {
            $this->Flash->error('Please answer this question to continue.');

            return $this->redirect('/take/' . $token . '?step=' . $step);
        }

        $draft = $this->draft($token);
        $draft['answers'][(string)$question->id] = $raw;
        $this->request->getSession()->write($this->sessionKey($token), $draft);

        if ($step >= $total) {
            return $this->persist($token, $invitation, $draft['answers']);
        }

        return $this->redirect('/take/' . $token . '?step=' . ($step + 1));
    }

    public function submit(string $token, ?ResponseRecorder $recorder = null): ?Response
    {
        $this->request->allowMethod(['post']);
        $invitation = $this->invitation($token);
        $draft = $this->draft($token);
        $posted = (array)$this->request->getData('answers');
        $answers = $posted + $draft['answers'];

        return $this->persist($token, $invitation, $answers, $recorder);
    }

    /**
     * @param array<int|string, mixed> $answers
     */
    private function persist(string $token, object $invitation, array $answers, ?ResponseRecorder $recorder = null): Response
    {
        $recorder ??= new ResponseRecorder();
        try {
            $recorder->record($invitation, $answers);
        } catch (\RuntimeException $e) {
            $this->Flash->error($e->getMessage());

            return $this->redirect('/take/' . $token);
        }
        $this->request->getSession()->delete($this->sessionKey($token));

        return $this->redirect('/take/' . $token);
    }

    private function invitation(string $token)
    {
        $invitation = $this->fetchTable('Survey.Invitations')
            ->find()
            ->applyOptions(['skipTenant' => true])
            ->where(['token' => $token])
            ->firstOrFail();
        Configure::write('Tenant.id', $invitation->tenant_id);
        $tenant = $this->fetchTable('AdminKit.Tenants')->get($invitation->tenant_id);
        Configure::write('Tenant.entity', $tenant);
        $this->set('currentTenant', $tenant);

        return $invitation;
    }

    private function survey(object $invitation)
    {
        return $this->fetchTable('Survey.Surveys')->get($invitation->survey_id, contain: ['Questions.QuestionOptions']);
    }

    /**
     * @return array{answers: array<string, mixed>}
     */
    private function draft(string $token): array
    {
        $draft = $this->request->getSession()->read($this->sessionKey($token));

        return is_array($draft) ? $draft + ['answers' => []] : ['answers' => []];
    }

    private function sessionKey(string $token): string
    {
        return 'take_' . $token;
    }
}
