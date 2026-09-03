<?php
declare(strict_types=1);

namespace Survey\Model\Entity;

use Cake\ORM\Entity;

class Invitation extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'survey_id' => true,
        'organisation_node_id' => true,
        'user_id' => true,
        'email' => true,
        'token' => true,
        'used' => true,
        'sent_at' => true,
        'sent_count' => true,
    ];
}
