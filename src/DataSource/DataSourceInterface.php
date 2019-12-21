<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\DataSource;

use FreezyBee\DataGridBundle\Column\Column;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
interface DataSourceInterface
{
    /**
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @param Column $column
     * @param string $direction
     */
    public function applySort(Column $column, string $direction): void;

    /**
     * @param Column $column
     * @param mixed $value
     */
    public function applyFilter(Column $column, $value): void;

    /**
     * @return int
     */
    public function getFilteredCount(): int;

    /**
     * @param int $limit
     * @param int $offset
     */
    public function applyLimitAndOffset(int $limit, int $offset): void;

    /**
     * @return mixed[]
     */
    public function getData(): array;
}
