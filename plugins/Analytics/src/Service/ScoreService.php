<?php
declare(strict_types=1);

namespace Analytics\Service;

use Cake\ORM\Locator\LocatorAwareTrait;

class ScoreService
{
    use LocatorAwareTrait;

    /**
     * @var array<string, CalculatorInterface>
     */
    private array $calculators;

    public function __construct(?AnonymityPolicy $policy = null)
    {
        $this->policy = $policy ?? new AnonymityPolicy();
        $this->calculators = [
            'percent_positive' => new PercentPositiveCalculator(),
            'enps' => new EnpsCalculator(),
            'demonstrated_importance' => new DemonstratedImportanceCalculator(),
            'engagement_index' => new EngagementIndexCalculator(),
        ];
    }

    private AnonymityPolicy $policy;

    /**
     * @return array<string, mixed>
     */
    public function run(int $surveyId, ?int $nodeId = null): array
    {
        $surveys = $this->fetchTable('Survey.Surveys');
        $survey = $surveys->get($surveyId, contain: ['Questions']);
        $answers = $this->loadAnswers($surveyId, $nodeId);
        $questionTitles = [];
        foreach ($survey->questions as $question) {
            $questionTitles[(int)$question->id] = $question->title;
        }
        $config = [
            'positive_min' => $survey->positive_min,
            'positive_max' => $survey->positive_max,
            'scale_min' => $survey->scale_min,
            'scale_max' => $survey->scale_max,
        ];
        $responseCount = $this->responseCount($surveyId, $nodeId);
        $results = [];
        foreach ($this->calculators as $name => $calculator) {
            $raw = $calculator->calculate($answers, $config);
            $results[$name] = $this->policy->redact($raw, $responseCount, (int)$survey->min_responses);
            $this->persist($survey, $nodeId, $name, $results[$name], $responseCount);
        }

        return [
            'survey_id' => $surveyId,
            'survey_title' => $survey->title,
            'organisation_node_id' => $nodeId,
            'response_count' => $responseCount,
            'min_responses' => (int)$survey->min_responses,
            'questions' => $questionTitles,
            'results' => $results,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadAnswers(int $surveyId, ?int $nodeId): array
    {
        $query = $this->fetchTable('Survey.Answers')->find()
            ->contain(['Questions', 'Responses'])
            ->matching('Responses', function ($q) use ($surveyId, $nodeId) {
                $q->where(['Responses.survey_id' => $surveyId]);
                if ($nodeId) {
                    $q->where(['Responses.organisation_node_id' => $nodeId]);
                }

                return $q;
            });

        $rows = [];
        foreach ($query as $answer) {
            $rows[] = [
                'question_id' => $answer->question_id,
                'nature' => $answer->question->nature ?? 'standard',
                'competency' => $answer->question->competency ?? null,
                'numeric_value' => $answer->numeric_value,
            ];
        }

        return $rows;
    }

    private function responseCount(int $surveyId, ?int $nodeId): int
    {
        $q = $this->fetchTable('Survey.Responses')->find()->where(['survey_id' => $surveyId]);
        if ($nodeId) {
            $q->where(['organisation_node_id' => $nodeId]);
        }

        return $q->count();
    }

    private function persist(object $survey, ?int $nodeId, string $calculator, array $result, int $count): void
    {
        $runs = $this->fetchTable('Analytics.ScoreRuns');
        $runs->save($runs->newEntity([
            'tenant_id' => $survey->tenant_id,
            'survey_id' => $survey->id,
            'organisation_node_id' => $nodeId,
            'calculator' => $calculator,
            'response_count' => $count,
            'suppressed' => !empty($result['suppressed']),
            'result_json' => json_encode($result),
        ]));
    }
}
