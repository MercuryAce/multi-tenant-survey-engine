<?php
declare(strict_types=1);

namespace Survey\Controller;

use Cake\Http\Response;

class QuestionsController extends AppController
{
    public function add(): ?Response
    {
        $questions = $this->fetchTable('Survey.Questions');
        $question = $questions->newEmptyEntity();
        if ($this->request->is('post')) {
            $question = $questions->patchEntity($question, $this->request->getData());
            if ($questions->save($question)) {
                $this->addDefaultOptions($question);
                $this->Flash->success('Question added.');

                return $this->redirect(['controller' => 'Surveys', 'action' => 'view', $question->survey_id]);
            }
            $this->Flash->error('Could not save question.');
        }
        $surveyId = (int)$this->request->getQuery('survey_id');
        $question->survey_id = $surveyId ?: null;
        $this->set(compact('question'));

        return null;
    }

    private function addDefaultOptions(\Survey\Model\Entity\Question $question): void
    {
        if (!in_array($question->type, ['likert', 'enps'], true)) {
            return;
        }
        $survey = $this->fetchTable('Survey.Surveys')->get($question->survey_id);
        $options = $this->fetchTable('Survey.QuestionOptions');
        for ($i = (int)$survey->scale_min; $i <= (int)$survey->scale_max; $i++) {
            $options->save($options->newEntity([
                'question_id' => $question->id,
                'label' => (string)$i,
                'value' => $i,
                'sort_order' => $i,
            ]));
        }
    }
}
