<?php
declare(strict_types=1);

namespace ActionPlanning\Model\Entity;

use Cake\ORM\Entity;

class ActionPlanTask extends Entity
{
    protected array $_accessible = [
        'action_plan_id' => true,
        'name' => true,
        'completed_date' => true,
    ];
}
