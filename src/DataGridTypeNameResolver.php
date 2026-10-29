<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\Attribute\AsDataGridType;
use InvalidArgumentException;
use ReflectionClass;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final class DataGridTypeNameResolver
{
    /**
     * @param string $className
     * @return string
     */
    public static function resolve(string $className): string
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Data grid type class "%s" does not exist.', $className));
        }

        $attribute = (new ReflectionClass($className))->getAttributes(AsDataGridType::class)[0] ?? null;
        $name = $attribute?->newInstance()->name;

        if ($name === null) {
            return self::defaultName($className);
        }

        if (preg_match('/^[a-zA-Z0-9_-]+$/', $name) !== 1) {
            throw new InvalidArgumentException(sprintf(
                'Invalid data grid name "%s" on "%s", only letters, digits, "-" and "_" are allowed.',
                $name,
                $className
            ));
        }

        return $name;
    }

    /**
     * @param string $className
     * @return string
     */
    private static function defaultName(string $className): string
    {
        $pos = strrpos($className, '\\');
        $shortName = $pos === false ? $className : substr($className, $pos + 1);

        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $shortName));
    }
}
