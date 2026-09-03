<?php
declare(strict_types=1);

namespace AdminKit\Model\Entity;

use Cake\ORM\Entity;

class Tenant extends Entity
{
    protected array $_accessible = [
        'name' => true,
        'slug' => true,
        'brand_name' => true,
        'support_email' => true,
        'logo_path' => true,
        'primary_color' => true,
        'settings' => true,
    ];

    public function setting(string $key, mixed $default = null): mixed
    {
        $data = is_string($this->settings) ? json_decode($this->settings, true) : ($this->settings ?? []);

        return $data[$key] ?? $default;
    }
}
