<?php

declare(strict_types = 1);

namespace Thad\Data\Services;

use Org\Arr\Arr;
use function array_intersect;

class SerializerApi extends Serializer
{
    public function toApi(): array
    {
        return $this->toArray();
    }

    protected function sendOnChange(): ?array
    {
        return null;
    }

    public function getDirtyApi(): ?array
    {
        $sendOnChange = $this->sendOnChange();

        // Change all the time?
        if ($sendOnChange === null) {
            return null;
        }

        return Arr::only($this->model->toArray(), $sendOnChange);
    }

    public function isUpdate(): bool
    {
        return $this->getDirtyApi() !== [];
    }
}
