<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\Controller\DataGridAjaxController;
use FreezyBee\DataGridBundle\Controller\DataGridExportController;
use FreezyBee\DataGridBundle\DependencyInjection\Compiler\GridTypePass;
use FreezyBee\DataGridBundle\Export\ChainExporter;
use FreezyBee\DataGridBundle\Export\DataGridExporterInterface;
use FreezyBee\DataGridBundle\Twig\DataGridExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final class FreezyBeeDataGridBundle extends AbstractBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new GridTypePass());
    }

    /**
     * @param mixed[] $config
     */
    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $services = $configurator->services()
            ->defaults()
                ->autowire()
                ->autoconfigure();

        $services->set(DataGridFactory::class);

        $services->load('FreezyBee\\DataGridBundle\\Export\\', __DIR__ . '/Export/*');

        $services->set(DataGridExtension::class);

        $services->set(DataGridAjaxController::class)
            ->public();

        $services->set(DataGridExportController::class)
            ->public();

        $services->alias(DataGridExporterInterface::class, ChainExporter::class);

        $container->registerForAutoconfiguration(DataGridTypeInterface::class)
            ->addTag('datagrid.type');

        $container->registerForAutoconfiguration(DataGridExporterInterface::class)
            ->addTag('datagrid.exporter');
    }
}
