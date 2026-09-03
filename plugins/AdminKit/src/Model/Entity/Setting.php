<?php
declare(strict_types=1);

namespace AdminKit\Model\Entity;

use Cake\ORM\Entity;

class Setting extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'name' => true,
        'value' => true,
    ];
}
