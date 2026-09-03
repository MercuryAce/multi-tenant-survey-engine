<?php
declare(strict_types=1);

namespace Survey\Model\Entity;

use Cake\ORM\Entity;

class Answer extends Entity
{
    protected array $_accessible = [
        'response_id' => true,
        'question_id' => true,
        'option_id' => true,
        'numeric_value' => true,
        'text_value' => true,
    ];
}
