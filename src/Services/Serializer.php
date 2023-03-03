<?php

declare(strict_types = 1);

namespace Thad\Data\Services;

use Thad\Data\BaseModel;
use Org\Arr\Arr;
use Org\Collection\Collection;
use function is_numeric;

class Serializer
{
    /**
     * @var BaseModel
     */
    protected $model;

    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    public function toArray(): array
    {
        return $this->model->toArray();
    }

    public function toCollection(): Collection
    {
        return new Collection($this->toArray());
    }

    public function toJson(int $options = 0): string
    {
        return $this->model->toJson($options);
    }

    protected function get(array $keysWithDefaults): array
    {
        $found = [];

        foreach ($keysWithDefaults as $key => $default) {

            // Index key? - set default to NULL, remove from array
            if (is_numeric($key)) {
                $key     = $default;
                $default = null;
            }

            $found[$key] = $this->model->{$key} ?? $default;
        }

        return $found;
    }

    protected function getMap(array $keysWithDefaults, array $mappings): array
    {
        $found = $this->get($keysWithDefaults);

        return Arr::map($found, $mappings);
    }
}
