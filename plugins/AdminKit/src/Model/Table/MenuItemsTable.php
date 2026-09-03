<?php
declare(strict_types=1);

namespace AdminKit\Model\Table;

use Cake\ORM\Table;

class MenuItemsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('menu_items');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Menus', ['className' => 'AdminKit.Menus']);
    }
}
