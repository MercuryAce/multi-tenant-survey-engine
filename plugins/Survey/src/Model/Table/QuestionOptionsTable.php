<?php
declare(strict_types=1);

namespace Survey\Model\Table;

use Cake\ORM\Table;

class QuestionOptionsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('question_options');
        $this->belongsTo('Questions', ['className' => 'Survey.Questions']);
    }
}
