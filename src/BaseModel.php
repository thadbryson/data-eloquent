<?php

declare(strict_types = 1);

namespace Data;

use Carbon\CarbonInterval;
use Data\Helpers\Services\Factory;
use Data\Models\Traits\BaseModel\FromMethods;
use Data\Models\Traits\BaseModel\MakeMethods;
use Data\Services;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Tool\Cast;
use Tool\Collection;
use Tool\Validation\Assert;
use function array_key_exists;
use function class_exists;

/**
 * Class BaseModel
 *
 * Base Model for Data project. Extent's Eloquent's Model.
 *
 * IMPORTANT: I still want to be able to use any Eloquent plugins, Admin systems,
 *            etc. So Configuration of the Models has to happen here mostly.
 *
 *            This includes: $table, $casts, $attributes (defaults), CREATED_AT, UPDATED_AT,
 *            soft deletes, $appends, etc.
 *
 * @property      int                             $id
 * @property      \Illuminate\Support\Carbon|null $created_at
 * @property      \Illuminate\Support\Carbon|null $updated_at
 * @property-read CarbonInterval|null             $created_at_interval
 * @property-read CarbonInterval|null             $updated_at_interval
 */
class BaseModel extends Model
{
    use FromMethods,
        MakeMethods;

    /**
     * Class name of Eloquent Event Observer class.
     *
     * @var string|null
     */
    protected static $observerClass;

    /**
     * Validator rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validator custom messages.
     *
     * @var array
     */
    protected $rulesMessages = [];

    /**
     * Validator custom attribute names.
     *
     * @var array
     */
    protected $rulesCustomAttributes = [];

    /**
     * Class name of custom Manager service.
     *
     * @var string|null
     */
    protected $managerClass;

    /**
     * Class name of custom Repository service.
     *
     * @var string|null
     */
    protected $repositoryClass;

    /**
     * Class name of custom Serializer service.
     *
     * @var string|null
     */
    protected $serializerClass;

    /**
     * Class name of custom SerializerApi service.
     *
     * @var string|null
     */
    protected $serializerApiClass;

    /**
     * Class name of custom Validator service.
     *
     * @var string|null
     */
    protected $validatorClass;

    /**
     * Get service Repository for an instance of this Model.
     *
     * @return Services\Repository
     */
    public static function repository(): Services\Repository
    {
        return static::make()->newRepository();
    }

    /**
     * Get service Repository for this Model.
     *
     * @return Services\Repository
     */
    public function newRepository(): Services\Repository
    {
        return Factory::make($this, 'Repository')->get($this->repositoryClass);
    }

    /**
     * Get service Serializer object.
     *
     * @return Services\Serializer
     */
    public function serializer(): Services\Serializer
    {
        return Factory::make($this, 'Serializer')->get($this->serializerClass);
    }

    /**
     * Get service SerializerApi object.
     *
     * @return Services\SerializerApi
     */
    public function serializerApi(): Services\SerializerApi
    {
        return Factory::make($this, 'SerializerApi')->get($this->serializerApiClass);
    }

    /**
     * Get service Validator object.
     *
     * @return Services\Validator
     */
    public function validator(): Services\Validator
    {
        return Factory::make($this, 'Validator')->get(
            $this->validatorClass,
            $this->rules,
            $this->rulesMessages,
            $this->rulesCustomAttributes
        );
    }

    /**
     * @inheritdoc
     */
    protected static function boot(): void
    {
        parent::boot();

        // Has an Observer class?
        $observerClass = static::$observerClass ?? static::class . '\\Observer';

        if (class_exists($observerClass)) {
            static::observe($observerClass);
        }
    }

    /**
     * Save Model and return $this for chaining.
     *
     * @param array $options = []
     * @return $this
     */
    public function saveThis(array $options = [])
    {
        $this->save($options);

        return $this;
    }

    /**
     * Use only these models, delete all others. Use 'id' of $replaceKey.
     *
     * @param string    $replaceKey
     * @param BaseModel ...$models
     * @return Collection
     * @throws \Tool\Validation\Exceptions\ValidationException
     */
    public static function formSetCollection(string $replaceKey, BaseModel ...$models): Collection
    {
        // All lights.
        $given = Collection::make($models)->keyBy($replaceKey);
        $keys  = $given->keys()->all();

        // DELETE anything we weren't just given.
        static::query()
            ->whereNotIn($replaceKey, $keys)
            ->delete();

        // UPDATE existing
        $existing = new Collection(
            static::query()
                ->whereIn($replaceKey, $keys)
                ->get()
                ->keyBy($replaceKey)
                ->map(function (BaseModel $model, $key) use (&$given) {

                    // Get & remove current BaseModel to update.
                    // So it won't be saved later.
                    $found = Assert::notNull($given->pull($key), sprintf('Model with key "%s" not found', $key));

                    $model->forceFill($found->toArray());

                    return $model->saveThis();
                })
        );

        // CREATE new models
        $given = $given->map(function (BaseModel $model) {

            return $model->saveThis();
        });

        return Collection::make($existing->values())
            ->merge($given->values())
            ->keyBy($replaceKey);
    }

    /**
     * Set Model's primary key.
     *
     * @param $id
     * @return $this
     */
    public function setPrimaryKey($id)
    {
        $key = $this->getKeyName();

        $this->{$key} = $id;

        return $this;
    }

    /**
     * Get an interval from the $created_at property.
     *
     * @return CarbonInterval|null
     */
    protected function getCreatedAtInterval(): ?CarbonInterval
    {
        if ($this->created_at === null) {
            return null;
        }

        return $this->created_at->diffAsCarbonInterval();
    }

    /**
     * Get an interval from the $updated_at property.
     *
     * @return CarbonInterval|null
     */
    protected function getUpdatedAtInterval(): ?CarbonInterval
    {
        if ($this->updated_at === null) {
            return null;
        }

        return $this->updated_at->diffAsCarbonInterval();
    }

    /**
     * Use some of my own casting rules.
     *
     * @param string $key
     * @param mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        if ($value === null) {
            return null;
        }

        switch ($this->getCastType($key)) {

            case 'collection':
                return Cast::toCollection($value);

            case 'datetime':
                return Cast::toDateTime($value);

            case 'bool':
            case 'boolean':
                return Cast::toBoolean($value);

            default:
                return parent::castAttribute($key, $value);
        }
    }

    protected function asDateTime($value)
    {
        return Cast::toDateTime($value);
    }

    /**
     * Does Model have this attribute?
     *
     * @param string $key
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributesToArray());
    }

    /**
     * Return attribute value or throw InvalidArgumentException if attribute does not exist.
     *
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function assertAttribute(string $key)
    {
        Assert::true($this->hasAttribute($key), sprintf('Attribute "%s" not found on Model %s', $key, static::class));

        return $this->getAttribute($key);
    }
}
