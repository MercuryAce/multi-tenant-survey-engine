<?php
declare(strict_types=1);

namespace Analytics\Service;

/**
 * @param list<array{question_id:int,nature:string,competency:?string,numeric_value:?int}> $answers
 */
interface CalculatorInterface
{
    public function name(): string;

    /**
     * @param list<array<string, mixed>> $answers
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    public function calculate(array $answers, array $config): array;
}
