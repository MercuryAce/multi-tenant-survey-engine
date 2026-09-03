<?php
declare(strict_types=1);

namespace Survey\Model\Table;

use Cake\ORM\Table;

class AnswersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('answers');
        $this->belongsTo('Responses', ['className' => 'Survey.Responses']);
        $this->belongsTo('Questions', ['className' => 'Survey.Questions']);
    }
}
