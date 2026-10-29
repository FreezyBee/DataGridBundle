<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Attribute;

use Attribute;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class AsDataGridType
{
    public function __construct(public string $name)
    {
    }
}
