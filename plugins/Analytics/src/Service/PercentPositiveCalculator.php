<?php
declare(strict_types=1);

namespace Analytics\Service;

class PercentPositiveCalculator implements CalculatorInterface
{
    public function name(): string
    {
        return 'percent_positive';
    }

    public function calculate(array $answers, array $config): array
    {
        $min = (int)($config['positive_min'] ?? 4);
        $max = (int)($config['positive_max'] ?? 5);
        $scored = array_values(array_filter($answers, fn($a) => $a['numeric_value'] !== null));
        $n = count($scored);
        if ($n === 0) {
            return ['percent_positive' => null, 'n' => 0];
        }
        $positive = count(array_filter($scored, fn($a) => $a['numeric_value'] >= $min && $a['numeric_value'] <= $max));
        $byQuestion = [];
        foreach ($scored as $row) {
            $qid = (int)$row['question_id'];
            $byQuestion[$qid]['n'] = ($byQuestion[$qid]['n'] ?? 0) + 1;
            if ($row['numeric_value'] >= $min && $row['numeric_value'] <= $max) {
                $byQuestion[$qid]['positive'] = ($byQuestion[$qid]['positive'] ?? 0) + 1;
            }
        }
        foreach ($byQuestion as $qid => $stats) {
            $byQuestion[$qid]['percent_positive'] = round(100 * ($stats['positive'] ?? 0) / $stats['n'], 1);
        }

        return [
            'percent_positive' => round(100 * $positive / $n, 1),
            'n' => $n,
            'by_question' => $byQuestion,
        ];
    }
}
