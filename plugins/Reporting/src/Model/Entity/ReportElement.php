<?php
declare(strict_types=1);

namespace Reporting\Model\Entity;

use Cake\ORM\Entity;

class ReportElement extends Entity
{
    protected array $_accessible = [
        'report_page_id' => true,
        'type' => true,
        'config_json' => true,
        'sort_order' => true,
    ];
}
