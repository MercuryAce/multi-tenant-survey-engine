<?php
declare(strict_types=1);

namespace Feedback360\Service;

use Cake\ORM\Locator\LocatorAwareTrait;

class CycleResultsService
{
    use LocatorAwareTrait;

    /**
     * @return array{assignments:int,completed:int,by_relationship:array<string,array{n:int,average:?float}>,subjects:array<int,array{name:string,n:int,average:?float}>}
     */
    public function summarise(object $cycle): array
    {
        $assignments = $cycle->rater_assignments ?? [];
        $completed = 0;
        $byRel = [];
        $subjects = [];

        foreach ($assignments as $row) {
            $rel = (string)$row->relationship;
            $byRel[$rel]['n'] = ($byRel[$rel]['n'] ?? 0);
            $byRel[$rel]['sum'] = $byRel[$rel]['sum'] ?? 0.0;
            $sid = (int)$row->subject_user_id;
            $subjects[$sid]['name'] = trim(($row->subject->first_name ?? '') . ' ' . ($row->subject->last_name ?? '')) ?: ('User #' . $sid);
            $subjects[$sid]['n'] = $subjects[$sid]['n'] ?? 0;
            $subjects[$sid]['sum'] = $subjects[$sid]['sum'] ?? 0.0;

            $invite = $row->invitation ?? null;
            if (!$invite || !$invite->used) {
                continue;
            }
            $completed++;
            $avg = $this->invitationAverage((int)$invite->id);
            if ($avg === null) {
                continue;
            }
            $byRel[$rel]['n']++;
            $byRel[$rel]['sum'] += $avg;
            $subjects[$sid]['n']++;
            $subjects[$sid]['sum'] += $avg;
        }

        $outRel = [];
        foreach ($byRel as $rel => $stats) {
            $outRel[$rel] = [
                'n' => (int)$stats['n'],
                'average' => $stats['n'] ? round($stats['sum'] / $stats['n'], 2) : null,
            ];
        }
        $outSubjects = [];
        foreach ($subjects as $id => $stats) {
            $outSubjects[$id] = [
                'name' => $stats['name'],
                'n' => (int)$stats['n'],
                'average' => $stats['n'] ? round($stats['sum'] / $stats['n'], 2) : null,
            ];
        }

        return [
            'assignments' => count(is_countable($assignments) ? $assignments : iterator_to_array($assignments)),
            'completed' => $completed,
            'by_relationship' => $outRel,
            'subjects' => $outSubjects,
        ];
    }

    private function invitationAverage(int $invitationId): ?float
    {
        $response = $this->fetchTable('Survey.Responses')
            ->find()
            ->where(['invitation_id' => $invitationId])
            ->first();
        if (!$response) {
            return null;
        }
        $answers = $this->fetchTable('Survey.Answers')
            ->find()
            ->where(['response_id' => $response->id, 'numeric_value IS NOT' => null])
            ->all();
        $values = [];
        foreach ($answers as $answer) {
            $values[] = (int)$answer->numeric_value;
        }
        if ($values === []) {
            return null;
        }

        return array_sum($values) / count($values);
    }
}
