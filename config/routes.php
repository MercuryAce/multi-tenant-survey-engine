<?php
declare(strict_types=1);

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->connect('/login', ['plugin' => 'AdminKit', 'controller' => 'Users', 'action' => 'login']);
        $builder->connect('/logout', ['plugin' => 'AdminKit', 'controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);

        $builder->connect('/tenants/onboard', ['plugin' => 'AdminKit', 'controller' => 'Tenants', 'action' => 'onboard']);
        $builder->connect('/tenants', ['plugin' => 'AdminKit', 'controller' => 'Tenants', 'action' => 'index']);
        $builder->connect('/tenants/{action}/*', ['plugin' => 'AdminKit', 'controller' => 'Tenants']);

        $builder->connect('/users', ['plugin' => 'AdminKit', 'controller' => 'Users', 'action' => 'index']);
        $builder->connect('/users/{action}/*', ['plugin' => 'AdminKit', 'controller' => 'Users']);
        $builder->connect('/roles', ['plugin' => 'AdminKit', 'controller' => 'Roles', 'action' => 'index']);
        $builder->connect('/roles/{action}/*', ['plugin' => 'AdminKit', 'controller' => 'Roles']);
        $builder->connect('/settings', ['plugin' => 'AdminKit', 'controller' => 'Settings', 'action' => 'index']);
        $builder->connect('/settings/{action}/*', ['plugin' => 'AdminKit', 'controller' => 'Settings']);

        $builder->connect('/organisations', ['plugin' => 'Organisation', 'controller' => 'Organisations', 'action' => 'index']);
        $builder->connect('/organisations/{action}/*', ['plugin' => 'Organisation', 'controller' => 'Organisations']);
        $builder->connect('/organisation-nodes', ['plugin' => 'Organisation', 'controller' => 'OrganisationNodes', 'action' => 'index']);
        $builder->connect('/organisation-nodes/{action}/*', ['plugin' => 'Organisation', 'controller' => 'OrganisationNodes']);

        $builder->connect('/surveys', ['plugin' => 'Survey', 'controller' => 'Surveys', 'action' => 'index']);
        $builder->connect('/surveys/{action}/*', ['plugin' => 'Survey', 'controller' => 'Surveys']);
        $builder->connect('/questions/{action}/*', ['plugin' => 'Survey', 'controller' => 'Questions']);
        $builder->connect('/invitations', ['plugin' => 'Survey', 'controller' => 'Invitations', 'action' => 'index']);
        $builder->connect('/invitations/{action}/*', ['plugin' => 'Survey', 'controller' => 'Invitations']);
        $builder->connect('/take/{token}', ['plugin' => 'Survey', 'controller' => 'TakeSurvey', 'action' => 'view'])
            ->setPass(['token']);
        $builder->connect('/take/{token}/step', ['plugin' => 'Survey', 'controller' => 'TakeSurvey', 'action' => 'step'])
            ->setPass(['token']);
        $builder->connect('/take/{token}/submit', ['plugin' => 'Survey', 'controller' => 'TakeSurvey', 'action' => 'submit'])
            ->setPass(['token']);

        $builder->connect('/analytics', ['plugin' => 'Analytics', 'controller' => 'Dashboards', 'action' => 'index']);
        $builder->connect('/analytics/{action}/*', ['plugin' => 'Analytics', 'controller' => 'Dashboards']);

        $builder->connect('/reports', ['plugin' => 'Reporting', 'controller' => 'Reports', 'action' => 'index']);
        $builder->connect('/reports/{action}/*', ['plugin' => 'Reporting', 'controller' => 'Reports']);
        $builder->connect('/report-templates/{action}/*', ['plugin' => 'Reporting', 'controller' => 'ReportTemplates']);

        $builder->connect('/feedback-cycles', ['plugin' => 'Feedback360', 'controller' => 'FeedbackCycles', 'action' => 'index']);
        $builder->connect('/feedback-cycles/{action}/*', ['plugin' => 'Feedback360', 'controller' => 'FeedbackCycles']);

        $builder->connect('/action-plans', ['plugin' => 'ActionPlanning', 'controller' => 'ActionPlans', 'action' => 'index']);
        $builder->connect('/action-plans/{action}/*', ['plugin' => 'ActionPlanning', 'controller' => 'ActionPlans']);

        $builder->connect('/pulses', ['plugin' => 'Pulse', 'controller' => 'Pulses', 'action' => 'index']);
        $builder->connect('/pulses/{action}/*', ['plugin' => 'Pulse', 'controller' => 'Pulses']);

        $builder->fallbacks();
    });
};
