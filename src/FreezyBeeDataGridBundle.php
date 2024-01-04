<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\DependencyInjection\Compiler\GridTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class FreezyBeeDataGridBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new GridTypePass());
    }
}
