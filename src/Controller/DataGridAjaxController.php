<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Controller;

use FreezyBee\DataGridBundle\DataGridFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final readonly class DataGridAjaxController
{
    public function __construct(
        private DataGridFactory $dataGridFactory
    ) {
    }

    /**
     * @param string $name
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(string $name, Request $request): JsonResponse
    {
        return $this->dataGridFactory->create($name)->ajax($request);
    }
}
