<?php
declare(strict_types=1);

namespace Survey\Model\Entity;

use Cake\ORM\Entity;

class QuestionOption extends Entity
{
    protected array $_accessible = [
        'question_id' => true,
        'label' => true,
        'value' => true,
        'sort_order' => true,
    ];
}
