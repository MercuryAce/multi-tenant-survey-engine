<?php
declare(strict_types=1);

namespace Organisation\Model\Entity;

use Cake\ORM\Entity;

class Organisation extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'name' => true,
        'logo_path' => true,
        'organisation_nodes' => true,
    ];
}
