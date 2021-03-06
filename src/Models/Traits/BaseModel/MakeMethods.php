<?php

declare(strict_types = 1);

namespace Data\Models\Traits\BaseModel;

use Data\BaseModel;
use Tool\Collection;

/**
 * Trait FromMethods
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin BaseModel
 */
trait MakeMethods
{
    /**
     * @param array $attributes
     * @return BaseModel|static
     */
    public static function make(array $attributes = []): BaseModel
    {
        $model = new static;

        foreach ($attributes as $attribute => $value) {

            if ($model->hasCast($attribute)) {
                $value = $model->castAttribute($attribute, $value);
            }

            $model->{$attribute} = $value;
        }

        return $model;
    }

    public static function makeCollection(iterable ...$attributes): Collection
    {
        $collection = new Collection;

        /** @var array $attributeCurrent */
        foreach ($attributes as $attributeCurrent) {
            $collection[] = static::make($attributeCurrent);
        }

        return $collection;
    }

    public function makeInstance(array $attributes = []): BaseModel
    {
        return static::make($attributes);
    }
}