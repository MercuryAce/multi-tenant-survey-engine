<?php
declare(strict_types=1);

namespace AdminKit\Model\Table;

use Cake\ORM\Table;

class SettingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('settings');
        $this->setDisplayField('name');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
    }

    public function put(int $tenantId, string $name, string $value): void
    {
        $row = $this->find()->where(['tenant_id' => $tenantId, 'name' => $name])->first();
        if (!$row) {
            $row = $this->newEntity(['tenant_id' => $tenantId, 'name' => $name]);
        }
        $row->value = $value;
        $this->saveOrFail($row);
    }
}
