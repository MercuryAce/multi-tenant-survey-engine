<?php
declare(strict_types=1);

namespace AdminKit\Model\Table;

use Cake\ORM\Table;

class RolesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('roles');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsToMany('Users', [
            'className' => 'AdminKit.Users',
            'joinTable' => 'users_roles',
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'user_id',
        ]);
    }
}
