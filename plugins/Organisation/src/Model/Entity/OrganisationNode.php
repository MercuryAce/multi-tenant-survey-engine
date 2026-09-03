<?php
declare(strict_types=1);

namespace Organisation\Model\Entity;

use Cake\ORM\Entity;

class OrganisationNode extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'organisation_id' => true,
        'parent_id' => true,
        'name' => true,
        'lft' => true,
        'rght' => true,
        'depth' => true,
        'quota' => true,
        'current_respondents' => true,
    ];
}
