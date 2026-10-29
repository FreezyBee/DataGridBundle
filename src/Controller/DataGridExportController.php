<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Controller;

use FreezyBee\DataGridBundle\DataGridFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final readonly class DataGridExportController
{
    public function __construct(
        private DataGridFactory $dataGridFactory
    ) {
    }

    /**
     * @param string $name
     * @param string $format
     * @param Request $request
     * @return Response
     */
    public function __invoke(string $name, string $format, Request $request): Response
    {
        return $this->dataGridFactory->create($name)->export($request, $format);
    }
}
