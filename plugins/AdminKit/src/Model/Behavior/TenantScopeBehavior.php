<?php
declare(strict_types=1);

namespace AdminKit\Model\Behavior;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;

class TenantScopeBehavior extends Behavior
{
    public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options, bool $primary): void
    {
        $tenantId = Configure::read('Tenant.id');
        if (!$tenantId || !empty($options['skipTenant'])) {
            return;
        }
        $table = $this->table();
        if (!$table->getSchema()->hasColumn('tenant_id')) {
            return;
        }
        $query->where([$table->aliasField('tenant_id') => $tenantId]);
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $tenantId = Configure::read('Tenant.id');
        if ($tenantId && $entity->isNew() && $entity->get('tenant_id') === null) {
            $entity->set('tenant_id', $tenantId);
        }
    }
}
