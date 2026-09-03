<?php
declare(strict_types=1);

namespace AdminKit\Controller;

use AdminKit\Service\TenantOnboardingService;
use Cake\Event\EventInterface;
use Cake\Http\Response;

class TenantsController extends CrudController
{
    protected string $resourceTable = 'AdminKit.Tenants';
    protected string $singular = 'tenant';
    protected string $plural = 'tenants';
    protected array $indexFields = ['id', 'name', 'slug', 'brand_name', 'support_email'];
    protected array $formFields = ['name', 'slug', 'brand_name', 'support_email', 'primary_color', 'logo_path'];

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['onboard']);
    }

    public function index(): void
    {
        $this->requireRole('super-admin');
        parent::index();
    }

    public function onboard(?TenantOnboardingService $onboarding = null): ?Response
    {
        $onboarding ??= new TenantOnboardingService();
        if ($this->request->is('post')) {
            try {
                $result = $onboarding->onboard($this->request->getData());
                $this->Flash->success('Tenant “' . $result['tenant']->name . '” is ready. You can log in as ' . $result['user']->email . '.');

                return $this->redirect('/login');
            } catch (\Throwable $e) {
                $this->Flash->error('Could not create tenant: ' . $e->getMessage());
            }
        }
        $this->viewBuilder()->setLayout('login');

        return null;
    }
}
