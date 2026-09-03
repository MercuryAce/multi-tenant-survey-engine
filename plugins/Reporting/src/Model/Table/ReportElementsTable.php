<?php
declare(strict_types=1);

namespace Reporting\Model\Table;

use Cake\ORM\Table;

class ReportElementsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('report_elements');
        $this->belongsTo('ReportPages', ['className' => 'Reporting.ReportPages']);
    }
}
