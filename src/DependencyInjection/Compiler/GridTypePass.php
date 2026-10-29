<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\DependencyInjection\Compiler;

use FreezyBee\DataGridBundle\DataGridFactory;
use FreezyBee\DataGridBundle\DataGridTypeNameResolver;
use FreezyBee\DataGridBundle\Export\ChainExporter;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final class GridTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $referencesByName = [];

        foreach (array_keys($container->findTaggedServiceIds('datagrid.type')) as $id) {
            $definition = $container->getDefinition($id)->setAutowired(true);

            $name = DataGridTypeNameResolver::resolve($definition->getClass() ?? $id);

            if (isset($referencesByName[$name])) {
                throw new LogicException(sprintf(
                    'Data grid name "%s" is used by both "%s" and "%s", data grid names must be unique.',
                    $name,
                    (string) $referencesByName[$name],
                    $id
                ));
            }

            $referencesByName[$name] = new Reference($id);
        }

        $container->getDefinition(DataGridFactory::class)
            ->setArgument('$typeLocator', ServiceLocatorTagPass::register($container, $referencesByName));

        $exporters = [];
        foreach ($container->findTaggedServiceIds('datagrid.exporter') as $id => $tag) {
            if ($id !== ChainExporter::class) {
                $exporters[] = $container->getDefinition((string) $id);
            }
        }

        $container->getDefinition(ChainExporter::class)->addArgument($exporters);
    }
}
