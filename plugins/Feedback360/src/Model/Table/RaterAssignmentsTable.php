<?php
declare(strict_types=1);

namespace Feedback360\Model\Table;

use Cake\ORM\Table;

class RaterAssignmentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('rater_assignments');
        $this->addBehavior('Timestamp');
        $this->belongsTo('FeedbackCycles', ['className' => 'Feedback360.FeedbackCycles']);
        $this->belongsTo('Subjects', ['className' => 'AdminKit.Users', 'foreignKey' => 'subject_user_id']);
        $this->belongsTo('Raters', ['className' => 'AdminKit.Users', 'foreignKey' => 'rater_user_id']);
        $this->belongsTo('Invitations', ['className' => 'Survey.Invitations']);
    }
}
