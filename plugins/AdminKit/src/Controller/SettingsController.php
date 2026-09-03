<?php
declare(strict_types=1);

namespace AdminKit\Controller;

use Cake\Http\Response;

class SettingsController extends AppController
{
    public function index(): ?Response
    {
        $this->requireRole('admin');
        $tenant = $this->fetchTable('AdminKit.Tenants')->get($this->request->getAttribute('identity')->get('tenant_id'));
        if ($this->request->is(['post', 'put'])) {
            $tenant = $this->fetchTable('AdminKit.Tenants')->patchEntity($tenant, $this->request->getData());
            if ($this->fetchTable('AdminKit.Tenants')->save($tenant)) {
                $this->Flash->success('Branding updated.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Could not save settings.');
        }
        $this->set(compact('tenant'));

        return null;
    }
}
