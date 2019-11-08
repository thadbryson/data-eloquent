<?php

declare(strict_types = 1);

namespace Data\Models\Traits;

use Data\BaseModel;

/**
 * Trait Singleton
 *
 * @mixin BaseModel
 */
trait Singleton
{
    public static function formSet(array $attributes): self
    {
        $instance = static::repository()->findInstance() ?? new static;
        $instance->forceFill($attributes);
        $instance->save();

        return $instance;
    }
}