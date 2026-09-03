<?php
declare(strict_types=1);

namespace Reporting\Model\Table;

use Cake\ORM\Table;

class ReportTemplatesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('report_templates');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Surveys', ['className' => 'Survey.Surveys']);
        $this->hasMany('ReportPages', ['className' => 'Reporting.ReportPages', 'sort' => ['ReportPages.sort_order' => 'ASC']]);
    }
}
