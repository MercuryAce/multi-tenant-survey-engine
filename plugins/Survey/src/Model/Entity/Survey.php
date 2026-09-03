<?php
declare(strict_types=1);

namespace Survey\Model\Entity;

use Cake\ORM\Entity;

class Survey extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'organisation_id' => true,
        'title' => true,
        'slug' => true,
        'kind' => true,
        'status' => true,
        'welcome_text' => true,
        'thankyou_text' => true,
        'scale_min' => true,
        'scale_max' => true,
        'positive_min' => true,
        'positive_max' => true,
        'min_responses' => true,
        'is_anonymous' => true,
        'starts' => true,
        'ends' => true,
        'questions' => true,
        'organisation' => true,
    ];
}
