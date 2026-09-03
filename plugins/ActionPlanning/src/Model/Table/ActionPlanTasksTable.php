<?php
declare(strict_types=1);

namespace ActionPlanning\Model\Table;

use Cake\ORM\Table;

class ActionPlanTasksTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('action_plan_tasks');
        $this->addBehavior('Timestamp');
        $this->belongsTo('ActionPlans', ['className' => 'ActionPlanning.ActionPlans']);
    }
}
