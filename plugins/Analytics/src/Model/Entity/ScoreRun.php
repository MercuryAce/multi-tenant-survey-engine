<?php
declare(strict_types=1);

namespace Analytics\Model\Entity;

use Cake\ORM\Entity;

class ScoreRun extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'survey_id' => true,
        'organisation_node_id' => true,
        'calculator' => true,
        'response_count' => true,
        'suppressed' => true,
        'result_json' => true,
    ];
}
