<?php
declare(strict_types=1);

namespace AdminKit\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

class UsersController extends CrudController
{
    protected string $resourceTable = 'AdminKit.Users';
    protected string $singular = 'user';
    protected string $plural = 'users';
    protected array $indexFields = ['id', 'email', 'first_name', 'last_name', 'is_active'];
    protected array $formFields = ['email', 'password', 'first_name', 'last_name', 'is_active'];

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login']);
        $this->FormProtection->setConfig('unlockedActions', ['login']);
    }

    public function login(): ?Response
    {
        $this->viewBuilder()->setLayout('login');
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $redirect = $this->request->getQuery('redirect', '/');

            return $this->redirect($redirect);
        }
        if ($this->request->is('post')) {
            $this->Flash->error('Invalid email or password.');
        }

        return null;
    }

    public function logout(): Response
    {
        $this->Authentication->logout();

        return $this->redirect('/login');
    }

    public function add(): ?Response
    {
        $this->requireRole('admin');

        return parent::add();
    }

    public function index(): void
    {
        $this->requireRole('admin');
        parent::index();
    }
}
