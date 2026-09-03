<?php
declare(strict_types=1);

namespace Survey\Model\Entity;

use Cake\ORM\Entity;

class Response extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'survey_id' => true,
        'invitation_id' => true,
        'user_id' => true,
        'organisation_node_id' => true,
        'submitted' => true,
        'answers' => true,
    ];
}
