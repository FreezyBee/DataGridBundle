<?php

declare(strict_types=1);

use FreezyBee\DataGridBundle\Controller\DataGridAjaxController;
use FreezyBee\DataGridBundle\Controller\DataGridExportController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('datagrid_ajax', '/datagrid/ajax/{name}')
        ->controller(DataGridAjaxController::class)
        ->requirements(['name' => '[a-zA-Z0-9_-]+']);

    $routes->add('datagrid_export', '/datagrid/export/{name}.{format}')
        ->controller(DataGridExportController::class)
        ->requirements([
            'name' => '[a-zA-Z0-9_-]+',
            'format' => '[a-zA-Z0-9]+',
        ]);
};
