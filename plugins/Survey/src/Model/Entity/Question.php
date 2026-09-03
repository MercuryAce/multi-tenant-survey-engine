<?php
declare(strict_types=1);

namespace Survey\Model\Entity;

use Cake\ORM\Entity;

class Question extends Entity
{
    protected array $_accessible = [
        'survey_id' => true,
        'nature' => true,
        'type' => true,
        'title' => true,
        'body' => true,
        'competency' => true,
        'sort_order' => true,
        'is_required' => true,
        'is_filter' => true,
        'is_reverse_scale' => true,
        'question_options' => true,
    ];
}
