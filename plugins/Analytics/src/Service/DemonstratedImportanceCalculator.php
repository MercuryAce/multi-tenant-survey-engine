<?php
declare(strict_types=1);

namespace Analytics\Service;

class DemonstratedImportanceCalculator implements CalculatorInterface
{
    public function name(): string
    {
        return 'demonstrated_importance';
    }

    public function calculate(array $answers, array $config): array
    {
        $groups = [];
        foreach ($answers as $row) {
            if ($row['numeric_value'] === null) {
                continue;
            }
            $key = (string)($row['competency'] ?: 'ungrouped');
            $nature = (string)$row['nature'];
            $groups[$key][$nature][] = (int)$row['numeric_value'];
        }
        $out = [];
        foreach ($groups as $competency => $natures) {
            $demo = $this->avg($natures['demonstrated'] ?? []);
            $imp = $this->avg($natures['importance'] ?? []);
            $out[$competency] = [
                'demonstrated' => $demo,
                'importance' => $imp,
                'gap' => ($demo !== null && $imp !== null) ? round($imp - $demo, 2) : null,
            ];
        }

        return ['competencies' => $out];
    }

    /**
     * @param list<int> $values
     */
    private function avg(array $values): ?float
    {
        if ($values === []) {
            return null;
        }

        return round(array_sum($values) / count($values), 2);
    }
}
