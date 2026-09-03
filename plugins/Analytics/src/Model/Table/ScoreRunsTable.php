<?php
declare(strict_types=1);

namespace Analytics\Model\Table;

use Cake\ORM\Table;

class ScoreRunsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('score_runs');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Surveys', ['className' => 'Survey.Surveys']);
    }
}
