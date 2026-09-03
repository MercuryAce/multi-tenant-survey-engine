<?php
declare(strict_types=1);

namespace Reporting\Model\Entity;

use Cake\ORM\Entity;

class ReportTemplate extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'survey_id' => true,
        'name' => true,
        'report_pages' => true,
        'survey' => true,
    ];
}
