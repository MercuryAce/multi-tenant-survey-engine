<?php
declare(strict_types=1);

namespace Reporting\Controller;

use App\Controller\AppController;
use Cake\Http\Response;
use Reporting\Service\ExportService;

class ReportsController extends AppController
{
    public function index(): void
    {
        $templates = $this->fetchTable('Reporting.ReportTemplates')->find()->contain(['Surveys'])->all();
        $this->set(compact('templates'));
    }

    public function add(): ?Response
    {
        $templates = $this->fetchTable('Reporting.ReportTemplates');
        $template = $templates->newEmptyEntity();
        $template->survey_id = $this->request->getQuery('survey_id') ? (int)$this->request->getQuery('survey_id') : null;
        if ($this->request->is('post')) {
            $template = $templates->patchEntity($template, $this->request->getData());
            if ($templates->save($template)) {
                $pages = $this->fetchTable('Reporting.ReportPages');
                $page = $pages->saveOrFail($pages->newEntity([
                    'report_template_id' => $template->id,
                    'title' => 'Overview',
                    'sort_order' => 1,
                ]));
                $elements = $this->fetchTable('Reporting.ReportElements');
                $elements->save($elements->newEntity([
                    'report_page_id' => $page->id,
                    'type' => 'static',
                    'config_json' => json_encode(['text' => 'Engagement overview for this survey.']),
                    'sort_order' => 1,
                ]));
                foreach (['percent_positive' => 2, 'enps' => 3, 'demonstrated_importance' => 4] as $calc => $order) {
                    $elements->save($elements->newEntity([
                        'report_page_id' => $page->id,
                        'type' => 'score',
                        'config_json' => json_encode(['calculator' => $calc]),
                        'sort_order' => $order,
                    ]));
                }
                $this->Flash->success('Template created.');

                return $this->redirect(['action' => 'preview', $template->id]);
            }
        }
        $this->set('surveys', $this->fetchTable('Survey.Surveys')->find('list')->all());
        $this->set(compact('template'));

        return null;
    }

    public function preview(string $id, ?ExportService $export = null): void
    {
        $export ??= new ExportService();
        $payload = $export->payload((int)$id);
        $this->viewBuilder()->setLayout('export');
        $this->set($payload);
        $this->set('html', $export->html($payload));
    }

    public function pdf(string $id, ?ExportService $export = null): Response
    {
        $export ??= new ExportService();
        $payload = $export->payload((int)$id);
        $this->disableAutoRender();

        return $this->response
            ->withType('pdf')
            ->withDownload($payload['template']->name . '.pdf')
            ->withStringBody($export->pdf($payload));
    }

    public function docx(string $id, ?ExportService $export = null): Response
    {
        $export ??= new ExportService();
        $payload = $export->payload((int)$id);

        return $this->response
            ->withType('docx')
            ->withDownload($payload['template']->name . '.docx')
            ->withStringBody($export->docx($payload));
    }

    public function pptx(string $id, ?ExportService $export = null): Response
    {
        $export ??= new ExportService();
        $payload = $export->payload((int)$id);

        return $this->response
            ->withType('pptx')
            ->withDownload($payload['template']->name . '.pptx')
            ->withStringBody($export->pptx($payload));
    }
}
