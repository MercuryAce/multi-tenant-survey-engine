<?php
declare(strict_types=1);

namespace Analytics\Service;

class EngagementIndexCalculator implements CalculatorInterface
{
    public function name(): string
    {
        return 'engagement_index';
    }

    public function calculate(array $answers, array $config): array
    {
        $pp = (new PercentPositiveCalculator())->calculate(
            array_values(array_filter(
                $answers,
                fn($a) => in_array($a['nature'] ?? 'standard', ['standard', 'demonstrated'], true),
            )),
            $config,
        );

        return [
            'engagement_index' => $pp['percent_positive'],
            'n' => $pp['n'],
        ];
    }
}
