<?php
declare(strict_types=1);

namespace Survey\Model\Table;

use Cake\ORM\Table;

class ResponsesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('responses');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Surveys', ['className' => 'Survey.Surveys']);
        $this->belongsTo('Invitations', ['className' => 'Survey.Invitations']);
        $this->belongsTo('OrganisationNodes', ['className' => 'Organisation.OrganisationNodes']);
        $this->hasMany('Answers', ['className' => 'Survey.Answers']);
    }
}
