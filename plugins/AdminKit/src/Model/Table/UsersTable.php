<?php
declare(strict_types=1);

namespace AdminKit\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('users');
        $this->setDisplayField('email');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Tenants', ['className' => 'AdminKit.Tenants']);
        $this->belongsToMany('Roles', [
            'className' => 'AdminKit.Roles',
            'joinTable' => 'users_roles',
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'role_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->email('email', false, 'Enter a valid email')
            ->notEmptyString('email')
            ->notEmptyString('first_name')
            ->notEmptyString('last_name');

        return $validator;
    }

    public function findAuth(SelectQuery $query): SelectQuery
    {
        return $query
            ->contain(['Roles'])
            ->where(['Users.is_active' => true]);
    }
}
