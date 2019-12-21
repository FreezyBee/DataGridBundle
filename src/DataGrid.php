<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle;

use FreezyBee\DataGridBundle\DataSource\DataSourceInterface;
use FreezyBee\DataGridBundle\Export\DataGridExporterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DataGrid
{
    /** @var Environment */
    private $engine;

    /** @var DataGridExporterInterface */
    private $exporter;

    /** @var DataSourceInterface */
    private $dataSource;

    /** @var DataGridConfig */
    private $config;

    /** @var string */
    private $name;

    /**
     * @param Environment $engine
     * @param DataGridExporterInterface $exporter
     * @param DataSourceInterface $dataSource
     * @param DataGridConfig $config
     * @param string $name
     */
    public function __construct(
        Environment $engine,
        DataGridExporterInterface $exporter,
        DataSourceInterface $dataSource,
        DataGridConfig $config,
        string $name
    ) {
        $this->engine = $engine;
        $this->exporter = $exporter;
        $this->dataSource = $dataSource;
        $this->config = $config;
        $this->name = $name;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ajax(Request $request): JsonResponse
    {
        $result = $this->processData($request, false, '');

        return (new JsonResponse([
            'draw' => $request->query->getInt('draw'),
            'recordsTotal' => $result['totalCount'],
            'recordsFiltered' => $result['filteredCount'],
            'data' => $result['data'],
        ]))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }


    /**
     * @param Request $request
     * @param string $format
     * @return Response
     */
    public function export(Request $request, string $format): Response
    {
        return $this->exporter->export($this->name, $this->processData($request, true, $format)['data'], $format);
    }

    /**
     * @param Request $request
     * @param bool $export
     * @param string $format
     * @return mixed[]
     */
    private function processData(Request $request, bool $export, string $format): array
    {
        $totalCount = $this->dataSource->getTotalCount();

        $query = $request->query->all();

        // sort
        $orderByIndex = $query['order'][0]['column'] ?? null;
        if ($orderByIndex !== null) {
            $this->dataSource->applySort($this->config->getRenderedColumns()[$orderByIndex], $query['order'][0]['dir']);
        }

        // filters
        foreach ($query['columns'] as $index => $ajaxColumn) {
            $value = $ajaxColumn['search']['value'] ?? '';
            $column = $this->config->getRenderedColumns()[$index] ?? null;

            if ($value !== '' && $column !== null && $column->isFilterable()) {
                $this->dataSource->applyFilter($column, $value);
            }
        }

        $filteredCount = $this->dataSource->getFilteredCount();

        if (!$export) {
            // limit and offset
            $this->dataSource->applyLimitAndOffset((int) $query['length'], (int) $query['start']);
        }

        $data = [];
        foreach ($this->dataSource->getData() as $item) {
            // custom export
            if ($export && $this->config->getCustomExportCallback() !== null) {
                $data[] = $this->config->getCustomExportCallback()($item);
                continue;
            }

            $row = [];
            foreach ($this->config->getColumns() as $column) {
                if ($export && $column->isAllowExport($format)) {
                    $row[$column->getLabel()] = $column->renderContent($item, $this->engine, ['export' => $format]);
                } elseif (!$export && $column->isAllowRender()) {
                    $row[] = $column->renderContent($item, $this->engine, ['export' => $export]);
                }
            }

            if (!$export && $this->config->getActionColumn()->hasActions()) {
                $row[] = $this->engine->render('@FreezyBeeDataGrid/action.html.twig', [
                    'item' => $item,
                    'actions' => $this->config->getActionColumn()->getActions()
                ]);
            }
            $data[] = $row;
        }

        return [
            'data' => $data,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
        ];
    }


    /**
     * @return string
     */
    public function render(): string
    {
        $sortIndex = 0;
        if ($this->config->getDefaultSortColumnName() !== null) {
            foreach ($this->config->getRenderedColumns() as $index => $column) {
                if ($column->getName() === $this->config->getDefaultSortColumnName()) {
                    $sortIndex = $index;
                    break;
                }
            }
        }

        return $this->engine->render('@FreezyBeeDataGrid/grid.html.twig', [
            'name' => $this->name,
            'columns' => $this->config->getRenderedColumns(),
            'actionColumn' => $this->config->getActionColumn(),
            'default' => [
                'perPage' => $this->config->getDefaultPerPage(),
                'sortIndex' => $sortIndex,
                'sortDir' => $this->config->getDefaultSortColumnDirection() ?? 'desc',
            ],
            'allowExport' => $this->config->getAllowExport(),
        ]);
    }
}
