<?php
declare(strict_types=1);

namespace App\Controller;

class DashboardController extends AppController
{
    public function index()
    {
        $surveys = $this->fetchTable('Survey.Surveys')
            ->find()
            ->orderBy(['Surveys.id' => 'DESC'])
            ->limit(8)
            ->all();
        $responses = $this->fetchTable('Survey.Responses')->find()->count();
        $nodes = $this->fetchTable('Organisation.OrganisationNodes')->find()->count();

        $this->set(compact('surveys', 'responses', 'nodes'));
    }
}
