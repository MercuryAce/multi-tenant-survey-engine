<?php
declare(strict_types=1);

namespace AdminKit;

use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;

class AdminKitPlugin extends BasePlugin
{
    public function routes(RouteBuilder $routes): void
    {
        parent::routes($routes);
    }
}
