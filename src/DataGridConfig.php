<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\Column\ActionColumn;
use FreezyBee\DataGridBundle\Column\Column;
use FreezyBee\DataGridBundle\DataSource\DataSourceInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGridConfig
{
    /** @var DataSourceInterface */
    private $dataSource;

    /** @var Column[] */
    private $columns;

    /** @var ActionColumn */
    private $actionColumn;

    /**
     * @var callable|null
     * callable(mixed $item)
     */
    private $customExportCallback;

    /** @var string|null */
    private $defaultSortColumnName;

    /** @var string|null */
    private $defaultSortColumnDirection;

    /** @var int */
    private $defaultPerPage;

    /** @var array<string, string> */
    private $allowExport;

    /**
     * @param DataSourceInterface $dataSource
     * @param Column[] $columns
     * @param ActionColumn $actionColumn
     * @param callable|null $customExportCallback
     * @param string|null $defaultSortColumnName
     * @param string|null $defaultSortColumnDirection
     * @param int $defaultPerPage
     * @param array<string, string> $allowExport
     */
    public function __construct(
        DataSourceInterface $dataSource,
        array $columns,
        ActionColumn $actionColumn,
        ?callable $customExportCallback,
        ?string $defaultSortColumnName,
        ?string $defaultSortColumnDirection,
        int $defaultPerPage,
        array $allowExport
    ) {
        $this->dataSource = $dataSource;
        $this->columns = $columns;
        $this->actionColumn = $actionColumn;
        $this->customExportCallback = $customExportCallback;
        $this->defaultSortColumnName = $defaultSortColumnName;
        $this->defaultSortColumnDirection = $defaultSortColumnDirection;
        $this->defaultPerPage = $defaultPerPage;
        $this->allowExport = $allowExport;
    }

    /**
     * @return DataSourceInterface
     */
    public function getDataSource(): DataSourceInterface
    {
        return $this->dataSource;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Column[]
     */
    public function getRenderedColumns(): array
    {
        return array_filter($this->columns, static function (Column $column) {
            return $column->isAllowRender();
        });
    }

    /**
     * @return ActionColumn
     */
    public function getActionColumn(): ActionColumn
    {
        return $this->actionColumn;
    }

    /**
     * @return callable|null
     */
    public function getCustomExportCallback(): ?callable
    {
        return $this->customExportCallback;
    }

    /**
     * @return string|null
     */
    public function getDefaultSortColumnName(): ?string
    {
        return $this->defaultSortColumnName;
    }

    /**
     * @return string|null
     */
    public function getDefaultSortColumnDirection(): ?string
    {
        return $this->defaultSortColumnDirection;
    }

    /**
     * @return int
     */
    public function getDefaultPerPage(): int
    {
        return $this->defaultPerPage;
    }

    /**
     * @return array<string, string>
     */
    public function getAllowExport(): array
    {
        return $this->allowExport;
    }
}
