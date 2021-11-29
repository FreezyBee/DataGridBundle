<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FreezyBee\DataGridBundle\FreezyBeeDataGridBundle;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        $bundles = [
            FrameworkBundle::class,
            TwigBundle::class,
            DoctrineBundle::class,
            FreezyBeeDataGridBundle::class,
        ];

        foreach ($bundles as $bundle) {
            yield new $bundle();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__ . '/../../src/Resources/config/routes.yaml');
        $routes->add('/', AppController::class . '::index');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerConfigurator $container): void
    {
        $services = $container->services();

        $services->set(AppController::class)
            ->public()
            ->autowire();

        $services->set(BeeGridType::class)
            ->autoconfigure();

        $services->alias(ContainerInterface::class, 'test.service_container');

        $container->extension('framework', [
            'secret' => 'x',
            'test' => true,
        ]);

        $container->extension('twig', [
            'strict_variables' => true,
            'paths' => [__DIR__ . '/templates/'],
        ]);

        $container->extension('doctrine', [
            'dbal' => [],
            'orm' => [],
        ]);
    }
}
