<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Column;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class ActionColumn extends Column
{
    /** @var Action[] */
    private $actions = [];

    /**
     */
    public function __construct()
    {
        parent::__construct('_actions', '');
    }

    /**
     * @param string $route
     * @param string $label
     * @param mixed[] $params
     * @return Action
     */
    public function addAction(string $route, string $label, array $params = []): Action
    {
        return $this->actions[] = new Action($route, $label, $params);
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return bool
     */
    public function hasActions(): bool
    {
        return count($this->actions) > 0;
    }
}
