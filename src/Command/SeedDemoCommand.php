<?php
declare(strict_types=1);

namespace App\Command;

use AdminKit\Service\TenantOnboardingService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

class SeedDemoCommand extends Command
{
    public static function defaultName(): string
    {
        return 'seed_demo';
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Create a demo tenant with a sample survey.');

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $onboarding = new TenantOnboardingService();
        $result = $onboarding->onboard([
            'name' => 'Demo Organisation',
            'slug' => 'demo',
            'brand_name' => 'Demo Organisation',
            'support_email' => 'support@example.com',
            'admin_email' => 'admin@example.com',
            'admin_password' => 'ChangeMe!23',
            'admin_first_name' => 'Ada',
            'admin_last_name' => 'Admin',
        ]);
        $tenant = $result['tenant'];
        Configure::write('Tenant.id', $tenant->id);

        $org = $this->fetchTable('Organisation.Organisations')->find()->where(['tenant_id' => $tenant->id])->firstOrFail();
        $surveys = $this->fetchTable('Survey.Surveys');
        $survey = $surveys->saveOrFail($surveys->newEntity([
            'tenant_id' => $tenant->id,
            'organisation_id' => $org->id,
            'title' => 'Engagement 2026',
            'slug' => 'engagement-2026',
            'kind' => 'engagement',
            'status' => 'open',
            'welcome_text' => 'Please tell us how you are finding work at the moment.',
            'thankyou_text' => 'Thank you for taking part.',
            'min_responses' => 4,
        ]));
        $questions = $this->fetchTable('Survey.Questions');
        $defs = [
            ['I am proud to work here', 'standard', 'Pride'],
            ['Leaders demonstrate our values', 'demonstrated', 'Leadership'],
            ['It is important that leaders demonstrate our values', 'importance', 'Leadership'],
            ['How likely are you to recommend us as a place to work?', 'enps', 'Advocacy'],
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
            $options = $this->fetchTable('Survey.QuestionOptions');
            $max = $nature === 'enps' ? 5 : 5;
            for ($v = 1; $v <= $max; $v++) {
                $options->save($options->newEntity([
                    'question_id' => $q->id,
                    'label' => (string)$v,
                    'value' => $v,
                    'sort_order' => $v,
                ]));
            }
        }
        $invite = $this->fetchTable('Survey.Invitations')->issue((int)$survey->id, null, 'respondent@example.com');

        $io->success('Demo tenant ready.');
        $io->out('Login: admin@example.com / ChangeMe!23');
        $io->out('Take survey: /take/' . $invite->token);

        return static::CODE_SUCCESS;
    }
}
