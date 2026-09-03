<?php
declare(strict_types=1);

namespace Feedback360\Model\Table;

use Cake\ORM\Table;

class FeedbackCyclesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('feedback_cycles');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Surveys', ['className' => 'Survey.Surveys']);
        $this->hasMany('RaterAssignments', ['className' => 'Feedback360.RaterAssignments']);
    }
}
