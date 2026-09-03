<?php
declare(strict_types=1);

namespace Analytics\Service;

class EnpsCalculator implements CalculatorInterface
{
    public function name(): string
    {
        return 'enps';
    }

    public function calculate(array $answers, array $config): array
    {
        $rows = array_values(array_filter(
            $answers,
            fn($a) => ($a['nature'] ?? '') === 'enps' && $a['numeric_value'] !== null,
        ));
        $n = count($rows);
        if ($n === 0) {
            return ['enps' => null, 'n' => 0, 'promoters' => 0, 'passives' => 0, 'detractors' => 0];
        }
        $scaleMax = (int)($config['scale_max'] ?? 5);
        $promoters = $passives = $detractors = 0;
        foreach ($rows as $row) {
            $bucket = $this->bucket((int)$row['numeric_value'], $scaleMax);
            if ($bucket === 'promoter') {
                $promoters++;
            } elseif ($bucket === 'passive') {
                $passives++;
            } else {
                $detractors++;
            }
        }

        return [
            'enps' => round(100 * ($promoters - $detractors) / $n, 1),
            'n' => $n,
            'promoters' => $promoters,
            'passives' => $passives,
            'detractors' => $detractors,
        ];
    }

    private function bucket(int $value, int $scaleMax): string
    {
        if ($scaleMax >= 10) {
            if ($value >= 9) {
                return 'promoter';
            }
            if ($value >= 7) {
                return 'passive';
            }

            return 'detractor';
        }
        if ($value >= $scaleMax) {
            return 'promoter';
        }
        if ($value === $scaleMax - 1) {
            return 'passive';
        }

        return 'detractor';
    }
}
