<?php
declare(strict_types=1);

namespace App\Test\TestCase\Calculator;

use Analytics\Service\AnonymityPolicy;
use Analytics\Service\DemonstratedImportanceCalculator;
use Analytics\Service\EngagementIndexCalculator;
use Analytics\Service\EnpsCalculator;
use Analytics\Service\PercentPositiveCalculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testPercentPositiveBand(): void
    {
        $calc = new PercentPositiveCalculator();
        $result = $calc->calculate([
            ['question_id' => 1, 'nature' => 'standard', 'competency' => null, 'numeric_value' => 5],
            ['question_id' => 1, 'nature' => 'standard', 'competency' => null, 'numeric_value' => 4],
            ['question_id' => 1, 'nature' => 'standard', 'competency' => null, 'numeric_value' => 2],
            ['question_id' => 2, 'nature' => 'standard', 'competency' => null, 'numeric_value' => 1],
        ], ['positive_min' => 4, 'positive_max' => 5]);

        $this->assertSame(50.0, $result['percent_positive']);
        $this->assertSame(4, $result['n']);
        $this->assertSame(66.7, $result['by_question'][1]['percent_positive']);
    }

    public function testEnpsOnFivePointScale(): void
    {
        $calc = new EnpsCalculator();
        $result = $calc->calculate([
            ['question_id' => 9, 'nature' => 'enps', 'numeric_value' => 5],
            ['question_id' => 9, 'nature' => 'enps', 'numeric_value' => 5],
            ['question_id' => 9, 'nature' => 'enps', 'numeric_value' => 4],
            ['question_id' => 9, 'nature' => 'enps', 'numeric_value' => 1],
        ], ['scale_max' => 5]);

        $this->assertSame(25.0, $result['enps']);
        $this->assertSame(2, $result['promoters']);
        $this->assertSame(1, $result['passives']);
        $this->assertSame(1, $result['detractors']);
    }

    public function testDemonstratedImportanceGap(): void
    {
        $calc = new DemonstratedImportanceCalculator();
        $result = $calc->calculate([
            ['question_id' => 1, 'nature' => 'demonstrated', 'competency' => 'Leadership', 'numeric_value' => 3],
            ['question_id' => 2, 'nature' => 'importance', 'competency' => 'Leadership', 'numeric_value' => 5],
        ], []);

        $this->assertSame(3.0, $result['competencies']['Leadership']['demonstrated']);
        $this->assertSame(5.0, $result['competencies']['Leadership']['importance']);
        $this->assertSame(2.0, $result['competencies']['Leadership']['gap']);
    }

    public function testEngagementIndexUsesStandardAndDemonstrated(): void
    {
        $calc = new EngagementIndexCalculator();
        $result = $calc->calculate([
            ['question_id' => 1, 'nature' => 'standard', 'competency' => null, 'numeric_value' => 5],
            ['question_id' => 2, 'nature' => 'enps', 'competency' => null, 'numeric_value' => 1],
        ], ['positive_min' => 4, 'positive_max' => 5]);

        $this->assertSame(100.0, $result['engagement_index']);
        $this->assertSame(1, $result['n']);
    }

    public function testAnonymityFloor(): void
    {
        $policy = new AnonymityPolicy();
        $this->assertTrue($policy->isSuppressed(3, 4));
        $this->assertFalse($policy->isSuppressed(4, 4));
        $redacted = $policy->redact(['percent_positive' => 80], 2, 4);
        $this->assertTrue($redacted['suppressed']);
        $this->assertArrayNotHasKey('percent_positive', $redacted);
    }
}
