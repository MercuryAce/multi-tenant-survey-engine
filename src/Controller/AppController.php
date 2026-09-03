<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('FormProtection');
        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => '/login',
        ]);
        $this->loadComponent('Authorization.Authorization');
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $identity = $this->request->getAttribute('identity');
        if ($identity) {
            $tenantId = $identity->get('tenant_id');
            Configure::write('Tenant.id', $tenantId);
            $tenants = $this->fetchTable('AdminKit.Tenants');
            $tenant = $tenants->find()->where(['id' => $tenantId])->first();
            Configure::write('Tenant.entity', $tenant);
            $this->set('currentTenant', $tenant);
            $this->set('currentUser', $identity);
            $this->set('navItems', $this->navFor($identity));
        } else {
            $this->set('currentTenant', null);
            $this->set('currentUser', null);
            $this->set('navItems', []);
        }
    }

    /**
     * @param object $identity
     * @return array<int, array{label:string,url:string}>
     */
    protected function navFor(object $identity): array
    {
        $items = [
            ['label' => 'Dashboard', 'url' => '/'],
            ['label' => 'Surveys', 'url' => '/surveys'],
            ['label' => 'Organisation', 'url' => '/organisation-nodes'],
            ['label' => 'Analytics', 'url' => '/analytics'],
            ['label' => 'Reports', 'url' => '/reports'],
            ['label' => '360', 'url' => '/feedback-cycles'],
            ['label' => 'Pulse', 'url' => '/pulses'],
            ['label' => 'Action plans', 'url' => '/action-plans'],
        ];

        $roles = $identity->get('roles') ?? [];
        $slugs = [];
        foreach ($roles as $role) {
            $slugs[] = is_array($role) ? ($role['slug'] ?? '') : (string)$role->slug;
        }
        if (in_array('super-admin', $slugs, true) || in_array('admin', $slugs, true)) {
            $items[] = ['label' => 'Users', 'url' => '/users'];
            $items[] = ['label' => 'Settings', 'url' => '/settings'];
            $items[] = ['label' => 'Tenants', 'url' => '/tenants'];
        }

        return $items;
    }

    protected function requireRole(string $slug): void
    {
        $identity = $this->request->getAttribute('identity');
        if (!$identity) {
            $this->redirect('/login');

            return;
        }
        foreach ($identity->get('roles') ?? [] as $role) {
            $roleSlug = is_array($role) ? ($role['slug'] ?? '') : (string)$role->slug;
            if ($roleSlug === $slug || $roleSlug === 'super-admin') {
                return;
            }
        }
        $this->Flash->error('You do not have access to that area.');
        $this->redirect('/');
    }
}
