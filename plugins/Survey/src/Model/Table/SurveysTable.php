<?php
declare(strict_types=1);

namespace Survey\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class SurveysTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('surveys');
        $this->setDisplayField('title');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Organisations', ['className' => 'Organisation.Organisations']);
        $this->hasMany('Questions', ['className' => 'Survey.Questions', 'sort' => ['Questions.sort_order' => 'ASC']]);
        $this->hasMany('Invitations', ['className' => 'Survey.Invitations']);
        $this->hasMany('Responses', ['className' => 'Survey.Responses']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->notEmptyString('title')->notEmptyString('slug');

        return $validator;
    }
}
