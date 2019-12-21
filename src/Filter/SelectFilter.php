<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Filter;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SelectFilter extends Filter
{
    /** @var mixed[] */
    private $options;

    /**
     * @param mixed[] $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
