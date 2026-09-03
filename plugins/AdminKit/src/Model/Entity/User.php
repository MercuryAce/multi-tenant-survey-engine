<?php
declare(strict_types=1);

namespace AdminKit\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

class User extends Entity
{
    protected array $_accessible = [
        'tenant_id' => true,
        'organisation_node_id' => true,
        'email' => true,
        'password' => true,
        'first_name' => true,
        'last_name' => true,
        'is_active' => true,
        'roles' => true,
    ];

    protected array $_hidden = ['password'];

    protected function _setPassword(?string $password): ?string
    {
        if ($password === null || $password === '') {
            return $this->password;
        }

        return (new DefaultPasswordHasher())->hash($password);
    }

    public function hasRole(string $slug): bool
    {
        foreach ($this->roles ?? [] as $role) {
            if ((string)$role->slug === $slug || (string)$role->slug === 'super-admin') {
                return true;
            }
        }

        return false;
    }
}
