<?php
declare(strict_types=1);

namespace Organisation\Model\Table;

use Cake\ORM\Table;

class OrganisationNodesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('organisation_nodes');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Organisations', ['className' => 'Organisation.Organisations']);
        $this->belongsTo('ParentOrganisationNodes', [
            'className' => 'Organisation.OrganisationNodes',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('ChildOrganisationNodes', [
            'className' => 'Organisation.OrganisationNodes',
            'foreignKey' => 'parent_id',
        ]);
    }
}
