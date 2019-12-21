<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Export;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
interface DataGridExporterInterface
{
    /**
     * @param string $gridType
     * @param mixed[] $data
     * @param string $format
     * @return Response
     */
    public function export(string $gridType, array $data, string $format): Response;

    /**
     * @param string $gridType
     * @param string $format
     * @return bool
     */
    public function supports(string $gridType, string $format): bool;
}
