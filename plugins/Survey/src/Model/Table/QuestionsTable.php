<?php
declare(strict_types=1);

namespace Survey\Model\Table;

use Cake\ORM\Table;

class QuestionsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('questions');
        $this->setDisplayField('title');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Surveys', ['className' => 'Survey.Surveys']);
        $this->hasMany('QuestionOptions', ['className' => 'Survey.QuestionOptions', 'sort' => ['QuestionOptions.sort_order' => 'ASC']]);
    }
}
