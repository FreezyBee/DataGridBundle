<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\DataSource;

use FreezyBee\DataGridBundle\Column\Column;
use FreezyBee\DataGridBundle\Filter\DateRangeFilter;
use FreezyBee\DataGridBundle\Filter\NumberRangeFilter;
use FreezyBee\DataGridBundle\Filter\SelectBooleanFilter;
use FreezyBee\DataGridBundle\Filter\SelectEntityFilter;
use FreezyBee\DataGridBundle\Filter\SelectFilter;
use FreezyBee\DataGridBundle\Utils\StringParserHelper;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class ArrayDataSource implements DataSourceInterface
{
    /** @var mixed[] */
    private $data;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /**
     * @param mixed[] $data
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(array $data, PropertyAccessorInterface $propertyAccessor)
    {
        $this->data = $data;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function applySort(Column $column, string $direction): void
    {
        foreach ($column->getSortColumnNames() as $propertyPath) {
            usort($this->data, function ($item1, $item2) use ($propertyPath, $direction): int {
                try {
                    $value1 = $this->accessValue($item1, $propertyPath);
                } catch (UnexpectedTypeException $e) {
                    $value1 = null;
                }

                try {
                    $value2 = $this->accessValue($item2, $propertyPath);
                } catch (UnexpectedTypeException $e) {
                    $value2 = null;
                }

                if (is_string($value1)) {
                    $value1 = mb_strtolower($value1);
                }

                if (is_string($value2)) {
                    $value2 = mb_strtolower($value2);
                }

                return ($direction === 'asc') ? $value1 <=> $value2 : $value2 <=> $value1;
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Column $column, $value): void
    {
        $filter = $column->getFilter();
        if ($filter !== null) {
            switch (true) {
                case $filter instanceof SelectFilter:
                    if ($filter instanceof SelectBooleanFilter) {
                        $value = (bool) $value;
                    }

                    $this->data = array_filter($this->data, function ($item) use ($column, $value): bool {
                        try {
                            foreach ($column->getFilterColumnNames() as $filterColumnName) {
                                if ($this->accessValue($item, $filterColumnName) === $value) {
                                    return true;
                                }
                            }
                        } catch (UnexpectedTypeException $e) {
                        }

                        return false;
                    });
                    return;
                case $filter instanceof SelectEntityFilter:
                    $propertyPath = "{$column->getFilterColumnNames()[0]}.id";
                    $value = (int) $value;

                    $this->data = array_filter($this->data, function ($item) use ($propertyPath, $value): bool {
                        try {
                            return $this->accessValue($item, $propertyPath) === $value;
                        } catch (UnexpectedTypeException $e) {
                            return false;
                        }
                    });
                    return;
                case $filter instanceof DateRangeFilter:
                    $propertyPath = $column->getContentColumnName();
                    [$from, $to] = StringParserHelper::parseStringToDateArray($value);

                    $this->data = array_filter($this->data, function ($item) use ($propertyPath, $from, $to): bool {
                        $existsValue = $this->accessValue($item, $propertyPath);
                        return ($existsValue >= $from && $existsValue <= $to);
                    });
                    return;
                case $filter instanceof NumberRangeFilter:
                    $propertyPath = $column->getContentColumnName();
                    [$from, $to] = StringParserHelper::parseStringToNumberArray($value);

                    $this->data = array_filter($this->data, function ($item) use ($propertyPath, $from, $to): bool {
                        $existsValue = $this->accessValue($item, $propertyPath);
                        return ($existsValue >= $from && $existsValue <= $to);
                    });
                    return;
            }
        }

        $propertyPath = $column->getContentColumnName();
        $value = mb_strtolower($value);

        $this->data = array_filter($this->data, function ($item) use ($propertyPath, $value): bool {
            $existsValue = (string) $this->accessValue($item, $propertyPath);
            return mb_stripos($existsValue, $value) !== false;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredCount(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function applyLimitAndOffset(int $limit, int $offset): void
    {
        $this->data = array_slice($this->data, $offset, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $item
     * @param string $propertyPath
     * @return mixed
     */
    public function accessValue($item, string $propertyPath)
    {
        if (is_array($item)) {
            $propertyPath = "[$propertyPath]";
        }
        return $this->propertyAccessor->getValue($item, $propertyPath);
    }
}
