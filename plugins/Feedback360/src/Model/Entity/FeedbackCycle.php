<?php
declare(strict_types=1);

namespace Feedback360\Model\Entity;

use Cake\ORM\Entity;

class FeedbackCycle extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'survey_id' => true,
        'name' => true,
        'status' => true,
        'rater_assignments' => true,
        'survey' => true,
    ];
}
