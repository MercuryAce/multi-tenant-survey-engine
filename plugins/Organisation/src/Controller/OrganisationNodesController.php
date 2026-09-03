<?php
declare(strict_types=1);

namespace Organisation\Controller;

use AdminKit\Controller\CrudController;

class OrganisationNodesController extends CrudController
{
    protected string $resourceTable = 'Organisation.OrganisationNodes';
    protected string $singular = 'organisationNode';
    protected string $plural = 'organisationNodes';
    protected array $indexFields = ['id', 'name', 'quota', 'current_respondents'];
    protected array $formFields = ['organisation_id', 'parent_id', 'name', 'quota'];

    public function index(): void
    {
        $nodes = $this->table()
            ->find('threaded')
            ->contain(['Organisations'])
            ->orderBy(['lft' => 'ASC'])
            ->all();
        $this->set(compact('nodes'));
    }

    public function add(): ?\Cake\Http\Response
    {
        $this->setLists();

        return parent::add();
    }

    public function edit(string $id): ?\Cake\Http\Response
    {
        $this->setLists();

        return parent::edit($id);
    }

    private function setLists(): void
    {
        $this->set('organisations', $this->fetchTable('Organisation.Organisations')->find('list')->all());
        $this->set('parentOrganisationNodes', $this->table()->find('list')->all());
    }
}
