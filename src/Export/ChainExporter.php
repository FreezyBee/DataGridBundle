<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Export;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class ChainExporter implements DataGridExporterInterface
{
    /** @var DataGridExporterInterface[] */
    private $exporters;

    /**
     * @param DataGridExporterInterface[] $exporters
     */
    public function __construct(array $exporters)
    {
        $this->exporters = $exporters;
    }

    /**
     * {@inheritdoc}
     */
    public function export(string $gridType, array $data, string $format): Response
    {
        foreach ($this->exporters as $exporter) {
            if ($exporter->supports($gridType, $format)) {
                return $exporter->export($gridType, $data, $format);
            }
        }

        throw new BadRequestHttpException();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $gridType, string $format): bool
    {
        return true;
    }
}
