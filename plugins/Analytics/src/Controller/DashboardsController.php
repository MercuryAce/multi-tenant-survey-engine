<?php
declare(strict_types=1);

namespace Analytics\Controller;

use Analytics\Service\ScoreService;
use App\Controller\AppController;

class DashboardsController extends AppController
{
    public function index(?ScoreService $scores = null): void
    {
        $scores ??= new ScoreService();
        $surveys = $this->fetchTable('Survey.Surveys')->find('list')->all();
        $surveyId = (int)($this->request->getQuery('survey_id') ?: $this->fetchTable('Survey.Surveys')->find()->select(['id'])->first()?->id);
        $report = null;
        if ($surveyId) {
            $report = $scores->run($surveyId);
        }
        $this->set(compact('surveys', 'surveyId', 'report'));
    }
}
