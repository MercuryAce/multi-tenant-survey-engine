<?php
declare(strict_types=1);

namespace Feedback360\Model\Entity;

use Cake\ORM\Entity;

class RaterAssignment extends Entity
{
    protected array $_accessible = [
        'feedback_cycle_id' => true,
        'subject_user_id' => true,
        'rater_user_id' => true,
        'relationship' => true,
        'invitation_id' => true,
        'invitation' => true,
    ];
}
