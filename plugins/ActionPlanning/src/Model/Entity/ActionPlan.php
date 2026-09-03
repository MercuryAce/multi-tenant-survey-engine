<?php
declare(strict_types=1);

namespace ActionPlanning\Model\Entity;

use Cake\ORM\Entity;

class ActionPlan extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'survey_id' => true,
        'organisation_node_id' => true,
        'user_id' => true,
        'name' => true,
        'details' => true,
        'status' => true,
        'start_date' => true,
        'end_date' => true,
        'action_plan_tasks' => true,
    ];
}
