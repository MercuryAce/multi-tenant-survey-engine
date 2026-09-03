<?php
declare(strict_types=1);

namespace Pulse\Controller;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Utility\Text;
use Survey\Service\InvitationService;

class PulsesController extends AppController
{
    public function index(): void
    {
        $pulses = $this->fetchTable('Survey.Surveys')
            ->find()
            ->where(['kind' => 'pulse'])
            ->contain(['Invitations', 'Responses', 'Questions'])
            ->all();
        $this->set(compact('pulses'));
    }

    public function add(): ?Response
    {
        $surveys = $this->fetchTable('Survey.Surveys');
        $pulse = $surveys->newEntity(['kind' => 'pulse', 'status' => 'draft']);
        if ($this->request->is('post')) {
            $data = $this->request->getData() + ['kind' => 'pulse'];
            $data['slug'] = $data['slug'] ?? Text::slug(strtolower((string)$data['title']));
            $pulse = $surveys->patchEntity($pulse, $data);
            if ($surveys->save($pulse)) {
                $this->addStarterQuestions($pulse);
                $this->Flash->success('Pulse survey created.');

                return $this->redirect(['action' => 'view', $pulse->id]);
            }
        }
        $this->set('organisations', $this->fetchTable('Organisation.Organisations')->find('list')->all());
        $this->set(compact('pulse'));

        return null;
    }

    public function view(string $id): ?Response
    {
        $pulse = $this->fetchTable('Survey.Surveys')->get($id, contain: ['Questions', 'Invitations', 'Responses']);
        if ($pulse->kind !== 'pulse') {
            $this->Flash->error('That survey is not a pulse.');

            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('post')) {
            $invites = new InvitationService();
            $created = $invites->issueMany(
                (int)$pulse->id,
                null,
                (string)$this->request->getData('email') . "\n" . (string)$this->request->getData('emails'),
                true,
            );
            if ($created === []) {
                $this->Flash->error('Enter at least one valid email address.');
            } else {
                $this->Flash->success(count($created) . ' invitation(s) created.');
            }

            return $this->redirect(['action' => 'view', $id]);
        }
        $this->set(compact('pulse'));

        return null;
    }

    public function open(string $id): Response
    {
        $this->request->allowMethod(['post']);
        $pulse = $this->fetchTable('Survey.Surveys')->get($id);
        $pulse->status = 'open';
        $this->fetchTable('Survey.Surveys')->saveOrFail($pulse);
        $this->Flash->success('Pulse is open.');

        return $this->redirect(['action' => 'view', $id]);
    }

    private function addStarterQuestions(object $pulse): void
    {
        $questions = $this->fetchTable('Survey.Questions');
        $options = $this->fetchTable('Survey.QuestionOptions');
        $defs = [
            ['I have what I need to do my job this week', 'standard', 'Enablement'],
            ['I would recommend this as a place to work', 'enps', 'Advocacy'],
        ];
        foreach ($defs as $i => [$title, $nature, $comp]) {
            $q = $questions->saveOrFail($questions->newEntity([
                'survey_id' => $pulse->id,
                'title' => $title,
                'nature' => $nature,
                'type' => $nature === 'enps' ? 'enps' : 'likert',
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
    }
}
