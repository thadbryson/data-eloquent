<?php

declare(strict_types = 1);

namespace Data\Helpers\Services;

class Factory
{
    /**
     * @var object
     */
    protected $model;

    /**
     * @var Finder
     */
    protected $finder;

    public function __construct(object $model, string $id)
    {
        $this->model = $model;

        $this->finder = new Finder($model, $id);
    }

    public static function make(object $model, string $id): self
    {
        return new static($model, $id);
    }

    public function getClass(?string $configuredClass): string
    {
        return $this->finder->get($configuredClass);
    }

    /**
     * @param object $model    - Model we need a service for.
     * @param string $id       - Service id of Service to build.
     * @param string $setClass = null - Configured class set on the Model to build from.
     * @param mixed  ...$args  - Any arguments passed to the Service constructor.
     * @return object
     */
    public function get(?string $configuredClass, ...$args): object
    {
        $serviceClass = $this->getClass($configuredClass);

        return new $serviceClass($this->model, ...$args);
    }
}