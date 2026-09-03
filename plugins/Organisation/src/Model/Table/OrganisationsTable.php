<?php
declare(strict_types=1);

namespace Organisation\Model\Table;

use Cake\ORM\Table;

class OrganisationsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('organisations');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->hasMany('OrganisationNodes', ['className' => 'Organisation.OrganisationNodes']);
    }
}
