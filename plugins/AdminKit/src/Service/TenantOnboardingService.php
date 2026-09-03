<?php
declare(strict_types=1);

namespace AdminKit\Service;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;

class TenantOnboardingService
{
    use LocatorAwareTrait;

    /**
     * Create a tenant, default roles, first admin, empty organisation, and starter menus.
     *
     * @param array<string, mixed> $data
     * @return array{tenant: EntityInterface, user: EntityInterface}
     */
    public function onboard(array $data): array
    {
        $tenants = $this->fetchTable('AdminKit.Tenants');
        $users = $this->fetchTable('AdminKit.Users');
        $roles = $this->fetchTable('AdminKit.Roles');
        $orgs = $this->fetchTable('Organisation.Organisations');
        $nodes = $this->fetchTable('Organisation.OrganisationNodes');

        $slug = $data['slug'] ?? Text::slug(strtolower((string)$data['name']));
        $tenant = $tenants->newEntity([
            'name' => $data['name'],
            'slug' => $slug,
            'brand_name' => $data['brand_name'] ?? $data['name'],
            'support_email' => $data['support_email'] ?? 'support@example.com',
            'primary_color' => $data['primary_color'] ?? '#1f4e79',
            'settings' => json_encode([
                'min_responses' => 4,
                'positive_min' => 4,
                'positive_max' => 5,
                'scale_min' => 1,
                'scale_max' => 5,
            ]),
        ]);
        $tenants->saveOrFail($tenant);

        $roleRows = [];
        foreach (['super-admin' => 'Super admin', 'admin' => 'Admin', 'manager' => 'Manager', 'respondent' => 'Respondent'] as $slugName => $label) {
            $roleRows[$slugName] = $roles->saveOrFail($roles->newEntity([
                'tenant_id' => $tenant->id,
                'name' => $label,
                'slug' => $slugName,
            ]));
        }

        $user = $users->newEntity([
            'tenant_id' => $tenant->id,
            'email' => $data['admin_email'],
            'password' => $data['admin_password'],
            'first_name' => $data['admin_first_name'] ?? 'Admin',
            'last_name' => $data['admin_last_name'] ?? 'User',
            'is_active' => true,
        ]);
        $user->roles = [$roleRows['super-admin']];
        $users->saveOrFail($user);

        $org = $orgs->saveOrFail($orgs->newEntity([
            'tenant_id' => $tenant->id,
            'name' => $data['organisation_name'] ?? $data['name'],
        ]));
        $nodes->saveOrFail($nodes->newEntity([
            'tenant_id' => $tenant->id,
            'organisation_id' => $org->id,
            'parent_id' => null,
            'name' => $org->name,
        ]));

        return compact('tenant', 'user');
    }
}
