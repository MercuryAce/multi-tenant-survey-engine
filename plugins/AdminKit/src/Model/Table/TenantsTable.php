<?php
declare(strict_types=1);

namespace AdminKit\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class TenantsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('tenants');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->hasMany('Users', ['className' => 'AdminKit.Users']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('name')
            ->notEmptyString('slug')
            ->email('support_email');

        return $validator;
    }
}
