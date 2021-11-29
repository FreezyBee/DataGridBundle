<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\Export\DataGridExporterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridFactory
{
    /** @var ContainerInterface */
    private $container;

    /** @var Environment */
    private $engine;

    /** @var DataGridExporterInterface */
    private $exporter;

    /**
     * @param ContainerInterface $container
     * @param Environment $engine
     * @param DataGridExporterInterface $exporter
     */
    public function __construct(
        ContainerInterface $container,
        Environment $engine,
        DataGridExporterInterface $exporter
    ) {
        $this->container = $container;
        $this->engine = $engine;
        $this->exporter = $exporter;
    }

    /**
     * @param string $className
     * @return DataGrid
     */
    public function create(string $className): DataGrid
    {
        /** @var DataGridTypeInterface $gridType */
        $gridType = $this->container->get($className);

        $builder = new DataGridBuilder();
        $gridType->buildGrid($builder);
        $config = $builder->generateConfig();

        return new DataGrid($this->engine, $this->exporter, $config->getDataSource(), $config, $className);
    }
}
