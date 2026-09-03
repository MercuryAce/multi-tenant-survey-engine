<?php
declare(strict_types=1);

namespace ActionPlanning\Model\Table;

use Cake\ORM\Table;

class ActionPlansTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('action_plans');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Surveys', ['className' => 'Survey.Surveys']);
        $this->belongsTo('Users', ['className' => 'AdminKit.Users']);
        $this->hasMany('ActionPlanTasks', ['className' => 'ActionPlanning.ActionPlanTasks']);
    }
}
