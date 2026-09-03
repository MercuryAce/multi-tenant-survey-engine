<?php
declare(strict_types=1);

namespace AdminKit\Controller;

use Cake\Http\Response;

abstract class CrudController extends AppController
{
    protected string $resourceTable = '';
    protected string $singular = 'entity';
    protected string $plural = 'entities';
    protected array $indexFields = ['id'];
    protected array $formFields = [];
    protected string $redirectAction = 'index';

    public function index(): void
    {
        $query = $this->table()->find();
        $this->set($this->plural, $this->paginate($query));
        $this->set('fields', $this->indexFields);
        $this->set('title', ucfirst($this->plural));
        $this->viewBuilder()->setTemplate('index');
    }

    public function view(string $id): void
    {
        $entity = $this->table()->get($id);
        $this->set($this->singular, $entity);
    }

    public function add(): ?Response
    {
        $entity = $this->table()->newEmptyEntity();
        if ($this->request->is('post')) {
            $entity = $this->table()->patchEntity($entity, $this->request->getData());
            if ($this->table()->save($entity)) {
                $this->Flash->success('Saved.');

                return $this->redirect(['action' => $this->redirectAction]);
            }
            $this->Flash->error('Could not save.');
        }
        $this->set($this->singular, $entity);
        $this->set('fields', $this->formFields);

        return null;
    }

    public function edit(string $id): ?Response
    {
        $entity = $this->table()->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $entity = $this->table()->patchEntity($entity, $this->request->getData());
            if ($this->table()->save($entity)) {
                $this->Flash->success('Saved.');

                return $this->redirect(['action' => $this->redirectAction]);
            }
            $this->Flash->error('Could not save.');
        }
        $this->set($this->singular, $entity);
        $this->set('fields', $this->formFields);

        return null;
    }

    public function delete(string $id): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $entity = $this->table()->get($id);
        if ($this->table()->delete($entity)) {
            $this->Flash->success('Deleted.');
        } else {
            $this->Flash->error('Could not delete.');
        }

        return $this->redirect(['action' => $this->redirectAction]);
    }

    protected function table(): \Cake\ORM\Table
    {
        return $this->fetchTable($this->resourceTable);
    }
}
