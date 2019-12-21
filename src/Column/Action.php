<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Column;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Action
{
    /** @var string */
    private $route;

    /** @var string */
    private $label;

    /** @var string|null */
    private $icon;

    /** @var string|null */
    private $class;

    /** @var mixed[] */
    private $params;

    /**
     * @param string $route
     * @param string $label
     * @param mixed[] $params
     */
    public function __construct(string $route, string $label, array $params)
    {
        $this->route = $route;
        $this->label = $label;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return Action
     */
    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string|null $class
     * @return Action
     */
    public function setClass(?string $class): self
    {
        $this->class = $class;
        return $this;
    }
}
