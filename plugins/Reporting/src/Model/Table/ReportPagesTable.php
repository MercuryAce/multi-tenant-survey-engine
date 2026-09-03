<?php
declare(strict_types=1);

namespace Reporting\Model\Table;

use Cake\ORM\Table;

class ReportPagesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('report_pages');
        $this->addBehavior('Timestamp');
        $this->belongsTo('ReportTemplates', ['className' => 'Reporting.ReportTemplates']);
        $this->hasMany('ReportElements', ['className' => 'Reporting.ReportElements']);
    }
}
