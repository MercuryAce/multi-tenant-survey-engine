<?php
declare(strict_types=1);

namespace Feedback360\Controller;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Utility\Text;
use Feedback360\Service\CycleResultsService;
use Survey\Service\InvitationService;

class FeedbackCyclesController extends AppController
{
    public function index(): void
    {
        $cycles = $this->fetchTable('Feedback360.FeedbackCycles')
            ->find()
            ->contain(['Surveys', 'RaterAssignments'])
            ->all();
        $this->set(compact('cycles'));
    }

    public function add(): ?Response
    {
        $table = $this->fetchTable('Feedback360.FeedbackCycles');
        $cycle = $table->newEmptyEntity();
        if ($this->request->is('post')) {
            $cycle = $table->patchEntity($cycle, $this->request->getData());
            if ($table->save($cycle)) {
                $this->Flash->success('360 cycle created. Add raters next.');

                return $this->redirect(['action' => 'view', $cycle->id]);
            }
        }
        $surveys = $this->fetchTable('Survey.Surveys')->find('list')->where(['kind' => '360'])->all();
        $this->set(compact('cycle', 'surveys'));

        return null;
    }

    public function starterSurvey(): Response
    {
        $this->request->allowMethod(['post']);
        $org = $this->fetchTable('Organisation.Organisations')->find()->first();
        if (!$org) {
            $this->Flash->error('Create an organisation before starting a 360 survey.');

            return $this->redirect(['action' => 'add']);
        }
        $surveys = $this->fetchTable('Survey.Surveys');
        $survey = $surveys->saveOrFail($surveys->newEntity([
            'organisation_id' => $org->id,
            'title' => '360 feedback',
            'slug' => '360-' . Text::slug(strtolower(uniqid())),
            'kind' => '360',
            'status' => 'open',
            'welcome_text' => 'Please rate this person based on what you have observed at work.',
            'thankyou_text' => 'Thank you. Your feedback stays confidential.',
            'min_responses' => 3,
            'is_anonymous' => true,
        ]));
        $questions = $this->fetchTable('Survey.Questions');
        $options = $this->fetchTable('Survey.QuestionOptions');
        $defs = [
            ['This person communicates clearly', 'standard', 'Communication'],
            ['This person demonstrates our values', 'demonstrated', 'Leadership'],
            ['It is important that this person demonstrates our values', 'importance', 'Leadership'],
        ];
        foreach ($defs as $i => [$title, $nature, $comp]) {
            $q = $questions->saveOrFail($questions->newEntity([
                'survey_id' => $survey->id,
                'title' => $title,
                'nature' => $nature,
                'type' => 'likert',
                'competency' => $comp,
                'sort_order' => $i + 1,
            ]));
            for ($v = 1; $v <= 5; $v++) {
                $options->save($options->newEntity([
                    'question_id' => $q->id,
                    'label' => (string)$v,
                    'value' => $v,
                    'sort_order' => $v,
                ]));
            }
        }
        $this->Flash->success('Starter 360 survey created. You can attach it to a cycle.');

        return $this->redirect(['action' => 'add']);
    }

    public function view(string $id, ?CycleResultsService $results = null): ?Response
    {
        $cycle = $this->cycle((int)$id);
        $users = $this->userList();
        if ($this->request->is('post')) {
            $this->assignRater($cycle, $this->request->getData());

            return $this->redirect(['action' => 'view', $id]);
        }
        $summary = ($results ?? new CycleResultsService())->summarise($cycle);
        $this->set(compact('cycle', 'users', 'summary'));

        return null;
    }

    public function open(string $id, ?InvitationService $invites = null): Response
    {
        $this->request->allowMethod(['post']);
        $invites ??= new InvitationService();
        $cycle = $this->cycle((int)$id);
        $cycle->status = 'open';
        $this->fetchTable('Feedback360.FeedbackCycles')->saveOrFail($cycle);
        $sent = 0;
        foreach ($cycle->rater_assignments as $row) {
            if ($row->invitation && !$row->invitation->used && !$row->invitation->sent_at && $invites->send($row->invitation)) {
                $sent++;
            }
        }
        $this->Flash->success($sent ? "Cycle opened. {$sent} invitation(s) emailed." : 'Cycle opened.');

        return $this->redirect(['action' => 'view', $id]);
    }

    public function close(string $id): Response
    {
        $this->request->allowMethod(['post']);
        $cycle = $this->cycle((int)$id);
        $cycle->status = 'closed';
        $this->fetchTable('Feedback360.FeedbackCycles')->saveOrFail($cycle);
        $this->Flash->success('Cycle closed.');

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assignRater(object $cycle, array $data): void
    {
        $assignments = $this->fetchTable('Feedback360.RaterAssignments');
        $users = $this->fetchTable('AdminKit.Users');
        $rater = $users->get((int)$data['rater_user_id']);
        $invites = new InvitationService();
        $send = $cycle->status === 'open';
        $invite = $invites->issueAndSend((int)$cycle->survey_id, null, $rater->email, $send, (int)$rater->id);
        $assignments->save($assignments->newEntity($data + [
            'feedback_cycle_id' => $cycle->id,
            'invitation_id' => $invite->id,
        ]));
        $this->Flash->success($send ? 'Rater assigned and invited.' : 'Rater assigned. Open the cycle to send invitations.');
    }

    private function cycle(int $id): object
    {
        return $this->fetchTable('Feedback360.FeedbackCycles')->get($id, contain: [
            'Surveys',
            'RaterAssignments.Subjects',
            'RaterAssignments.Raters',
            'RaterAssignments.Invitations',
        ]);
    }

    private function userList()
    {
        return $this->fetchTable('AdminKit.Users')->find('list', valueField: function ($u) {
            return trim($u->first_name . ' ' . $u->last_name) . ' (' . $u->email . ')';
        })->all();
    }
}
