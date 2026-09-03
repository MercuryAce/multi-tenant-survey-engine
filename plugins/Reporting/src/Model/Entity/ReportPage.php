<?php
declare(strict_types=1);

namespace Reporting\Model\Entity;

use Cake\ORM\Entity;

class ReportPage extends Entity
{
    protected array $_accessible = [
        'report_template_id' => true,
        'title' => true,
        'sort_order' => true,
        'report_elements' => true,
    ];
}
