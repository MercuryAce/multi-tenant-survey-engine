<?php
declare(strict_types=1);

namespace App\Test\TestCase\Survey;

use AdminKit\Service\TenantOnboardingService;
use Analytics\Service\ScoreService;
use Cake\Core\Configure;
use Cake\Mailer\TransportFactory;
use Cake\TestSuite\TestCase;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Utility\Text;
use Reporting\Service\ExportService;
use Survey\Service\InvitationService;
use Survey\Service\ResponseRecorder;

class LivePathTest extends TestCase
{
    use IntegrationTestTrait;

    private int $tenantId;
    private int $surveyId;
    private string $suffix;

    public function setUp(): void
    {
        parent::setUp();
        $this->suffix = strtolower(bin2hex(random_bytes(4)));
        TransportFactory::drop('default');
        TransportFactory::setConfig('default', [
            'className' => 'Debug',
        ]);
    }

    public function tearDown(): void
    {
        Configure::delete('Tenant.id');
        parent::tearDown();
    }

    public function testOnboardInviteSubmitThenScore(): void
    {
        $this->buildSurvey(minResponses: 2);

        $inviteOne = (new InvitationService())->issueAndSend(
            $this->surveyId,
            null,
            'r1-' . $this->suffix . '@example.com',
            true,
        );
        $this->assertNotEmpty($inviteOne->token);
        $this->assertNotEmpty($inviteOne->sent_at);

        $questions = $this->getTableLocator()->get('Survey.Questions')
            ->find()
            ->where(['survey_id' => $this->surveyId])
            ->orderBy(['sort_order' => 'ASC'])
            ->all();
        $answers = [];
        foreach ($questions as $i => $question) {
            $answers[$question->id] = $i === 3 ? 5 : 4;
        }

        (new ResponseRecorder())->record($inviteOne, $answers);
        $inviteOne = $this->getTableLocator()->get('Survey.Invitations')->get($inviteOne->id);
        $this->assertTrue((bool)$inviteOne->used);

        $first = (new ScoreService())->run($this->surveyId);
        $this->assertSame(1, $first['response_count']);
        $this->assertTrue($first['results']['percent_positive']['suppressed']);

        $inviteTwo = (new InvitationService())->issueAndSend(
            $this->surveyId,
            null,
            'r2-' . $this->suffix . '@example.com',
            false,
        );
        (new ResponseRecorder())->record($inviteTwo, $answers);

        $second = (new ScoreService())->run($this->surveyId);
        $this->assertSame(2, $second['response_count']);
        $this->assertFalse($second['results']['percent_positive']['suppressed']);
        $this->assertSame(100.0, $second['results']['percent_positive']['percent_positive']);
        $this->assertSame(100.0, $second['results']['engagement_index']['engagement_index']);
        $this->assertNotNull($second['results']['enps']['enps']);
    }

    public function testReportPdfContainsPages(): void
    {
        $this->buildSurvey(minResponses: 1);
        $invite = $this->getTableLocator()->get('Survey.Invitations')->issue($this->surveyId, null, 'pdf-' . $this->suffix . '@example.com');
        $questions = $this->getTableLocator()->get('Survey.Questions')->find()->where(['survey_id' => $this->surveyId])->all();
        $answers = [];
        foreach ($questions as $question) {
            $answers[$question->id] = 5;
        }
        (new ResponseRecorder())->record($invite, $answers);

        $templates = $this->getTableLocator()->get('Reporting.ReportTemplates');
        $template = $templates->saveOrFail($templates->newEntity([
            'tenant_id' => $this->tenantId,
            'survey_id' => $this->surveyId,
            'name' => 'Pack ' . $this->suffix,
        ]));
        $pages = $this->getTableLocator()->get('Reporting.ReportPages');
        $page = $pages->saveOrFail($pages->newEntity([
            'report_template_id' => $template->id,
            'title' => 'Overview',
            'sort_order' => 1,
        ]));
        $elements = $this->getTableLocator()->get('Reporting.ReportElements');
        $elements->saveOrFail($elements->newEntity([
            'report_page_id' => $page->id,
            'type' => 'score',
            'config_json' => json_encode(['calculator' => 'percent_positive']),
            'sort_order' => 1,
        ]));

        $export = new ExportService();
        $payload = $export->payload((int)$template->id);
        $pdf = $export->pdf($payload);
        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertGreaterThan(800, strlen($pdf));
        $this->assertStringContainsString('Percent positive', $export->html($payload));

        $docx = $export->docx($payload);
        $this->assertGreaterThan(1000, strlen($docx));
        $pptx = $export->pptx($payload);
        $this->assertGreaterThan(1000, strlen($pptx));
    }

    public function testTakeSurveyStepFlowRecordsResponse(): void
    {
        $this->buildSurvey(minResponses: 1);

        $invite = (new InvitationService())->issueAndSend(
            $this->surveyId,
            null,
            'step-' . $this->suffix . '@example.com',
            false,
        );
        $token = (string)$invite->token;

        $questions = $this->getTableLocator()->get('Survey.Questions')
            ->find()
            ->where(['survey_id' => $this->surveyId])
            ->orderBy(['sort_order' => 'ASC'])
            ->all();

        $i = 0;
        foreach ($questions as $question) {
            $i++;
            $this->enableCsrfToken();
            $this->post('/take/' . $token . '/step', [
                'step' => $i,
                'answer' => '5',
            ]);
        }

        $updatedInvite = $this->getTableLocator()->get('Survey.Invitations')->get($invite->id);
        $this->assertTrue((bool)$updatedInvite->used);

        $responsesCount = $this->getTableLocator()->get('Survey.Responses')
            ->find()
            ->where(['survey_id' => $this->surveyId])
            ->count();
        $this->assertSame(1, (int)$responsesCount);

        $score = (new ScoreService())->run($this->surveyId);
        $this->assertFalse($score['results']['percent_positive']['suppressed']);
        $this->assertSame(100.0, $score['results']['percent_positive']['percent_positive']);
    }

    private function buildSurvey(int $minResponses): void
    {
        $result = (new TenantOnboardingService())->onboard([
            'name' => 'Live Path ' . $this->suffix,
            'slug' => 'live-' . $this->suffix,
            'admin_email' => 'admin-' . $this->suffix . '@example.com',
            'admin_password' => 'ChangeMe!23',
            'admin_first_name' => 'Test',
            'admin_last_name' => 'Admin',
        ]);
        $this->tenantId = (int)$result['tenant']->id;
        Configure::write('Tenant.id', $this->tenantId);

        $org = $this->getTableLocator()->get('Organisation.Organisations')
            ->find()
            ->where(['tenant_id' => $this->tenantId])
            ->firstOrFail();
        $surveys = $this->getTableLocator()->get('Survey.Surveys');
        $survey = $surveys->saveOrFail($surveys->newEntity([
            'tenant_id' => $this->tenantId,
            'organisation_id' => $org->id,
            'title' => 'Live Path Survey',
            'slug' => 'live-path-' . $this->suffix,
            'kind' => 'engagement',
            'status' => 'open',
            'min_responses' => $minResponses,
        ]));
        $this->surveyId = (int)$survey->id;

        $questions = $this->getTableLocator()->get('Survey.Questions');
        $options = $this->getTableLocator()->get('Survey.QuestionOptions');
        $defs = [
            ['I am proud to work here', 'standard', 'Pride'],
            ['Leaders demonstrate our values', 'demonstrated', 'Leadership'],
            ['It is important that leaders demonstrate our values', 'importance', 'Leadership'],
            ['How likely are you to recommend us?', 'enps', 'Advocacy'],
        ];
        foreach ($defs as $i => [$title, $nature, $comp]) {
            $q = $questions->saveOrFail($questions->newEntity([
                'survey_id' => $survey->id,
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
