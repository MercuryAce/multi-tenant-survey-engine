<?php
declare(strict_types=1);

namespace Survey\Controller;

use AdminKit\Controller\CrudController;
use Cake\Http\Response;
use Cake\Utility\Text;

class SurveysController extends CrudController
{
    protected string $resourceTable = 'Survey.Surveys';
    protected string $singular = 'survey';
    protected string $plural = 'surveys';
    protected array $formFields = [
        'title', 'slug', 'kind', 'status', 'organisation_id',
        'welcome_text', 'thankyou_text', 'scale_min', 'scale_max',
        'positive_min', 'positive_max', 'min_responses', 'is_anonymous',
    ];

    public function index(): void
    {
        $surveys = $this->paginate($this->table()->find()->contain(['Organisations']));
        $this->set(compact('surveys'));
    }

    public function add(): ?Response
    {
        $this->set('organisations', $this->fetchTable('Organisation.Organisations')->find('list')->all());
        $survey = $this->table()->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['slug'] = $data['slug'] ?? Text::slug(strtolower((string)($data['title'] ?? 'survey')));
            $survey = $this->table()->patchEntity($survey, $data);
            if ($this->table()->save($survey)) {
                $this->Flash->success('Survey created.');

                return $this->redirect(['action' => 'view', $survey->id]);
            }
            $this->Flash->error('Could not save survey.');
        }
        $this->set(compact('survey'));

        return null;
    }

    public function edit(string $id): ?Response
    {
        $this->set('organisations', $this->fetchTable('Organisation.Organisations')->find('list')->all());

        return parent::edit($id);
    }

    public function view(string $id): void
    {
        $survey = $this->table()->get($id, contain: ['Questions', 'Organisations', 'Invitations', 'Responses']);
        $this->set(compact('survey'));
    }
}
