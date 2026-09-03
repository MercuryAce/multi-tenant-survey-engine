<?php
declare(strict_types=1);

namespace AdminKit\Controller;

class RolesController extends CrudController
{
    protected string $resourceTable = 'AdminKit.Roles';
    protected string $singular = 'role';
    protected string $plural = 'roles';
    protected array $indexFields = ['id', 'name', 'slug'];
    protected array $formFields = ['name', 'slug'];

    public function index(): void
    {
        $this->requireRole('admin');
        parent::index();
    }
}
