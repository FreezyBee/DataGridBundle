<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\Export\DataGridExporterInterface;
use Psr\Container\ContainerInterface;
use Twig\Environment;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final readonly class DataGridFactory
{
    public function __construct(
        private ContainerInterface $typeLocator,
        private Environment $engine,
        private DataGridExporterInterface $exporter
    ) {
    }

    /**
     * @param string $name grid name, or grid type class name
     * @return DataGrid
     */
    public function create(string $name): DataGrid
    {
        if (class_exists($name)) {
            $name = DataGridTypeNameResolver::resolve($name);
        }

        /** @var DataGridTypeInterface $gridType */
        $gridType = $this->typeLocator->get($name);

        $builder = new DataGridBuilder();
        $gridType->buildGrid($builder);
        $config = $builder->generateConfig();

        return new DataGrid($this->engine, $this->exporter, $config->getDataSource(), $config, $name);
    }
}
