<?php
declare(strict_types=1);

namespace AdminKit\Model\Table;

use Cake\ORM\Table;

class MenusTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('menus');
        $this->addBehavior('Timestamp');
        $this->hasMany('MenuItems', ['className' => 'AdminKit.MenuItems']);
    }
}
