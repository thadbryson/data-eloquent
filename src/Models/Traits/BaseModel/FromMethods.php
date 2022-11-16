<?php

declare(strict_types = 1);

namespace Data\Models\Traits\BaseModel;

use Data\BaseModel;
use Org\Tool\Arr;
use Org\Tool\Collection;
use Org\Tool\Request;
use Org\Tool\StrStatic;
use Org\Tool\Validation\Assert;

/**
 * Trait FromMethods
 *
 * @mixin BaseModel
 */
trait FromMethods
{
    public static function fromArrayMany(array $collection, string $keyMap = null): Collection
    {
        return Collection::make($collection)
            ->map(function (array $attributes, $key) use ($keyMap) {

                if ($keyMap !== null) {
                    $attributes[$keyMap] = $key;
                }

                return static::make($attributes);
            });
    }

    /**
     * @param array $attributes
     * @param array $map
     * @return BaseModel|static
     */
    public static function fromMap(array $attributes, array $map): BaseModel
    {
        $attributes = Arr::mapOnly($attributes, $map);

        return static::make($attributes);
    }

    /**
     * @param array $collection
     * @param array $map
     * @return Collection|static[]
     */
    public static function fromMapMany(array $collection, array $map, string $keyMap = null): Collection
    {
        $collection = Arr::mapEachOnly($collection, $map);

        return Collection::make($collection)
            ->map(function (array $attributes, $key) use ($keyMap) {

                if ($keyMap !== null) {
                    $attributes[$keyMap] = $key;
                }

                return static::make($attributes);
            });
    }

    public static function fromJsonArray(string $json): BaseModel
    {
        /** @var array $attributes */
        $attributes = StrStatic::jsonDecode($json);

        Assert::isArray($attributes, 'JSON did not decode to an array.');

        return static::make($attributes);
    }

    public static function fromJsonCollection(string $json): Collection
    {
        /** @var array $attributes */
        $decoded = StrStatic::jsonDecode($json);

        Assert::isArray($decoded, 'JSON did not decode to an array.');

        $collection = new Collection;

        foreach ($decoded as $index => $attributes) {

            Assert::isArray($attributes, sprintf('JSON at index "%s" is not an array of attributes.', $index));

            $collection[$index] = static::make($attributes);
        }

        return $collection;
    }

    public static function fromRequest(string $formDot = 'form'): BaseModel
    {
        $attributes = (array) Request::findOrFail($formDot);

        return static::make($attributes);
    }
}
