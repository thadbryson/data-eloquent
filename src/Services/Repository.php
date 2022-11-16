<?php

declare(strict_types = 1);

namespace Data\Services;

use Data\BaseModel;
use Data\Exceptions\ModelNotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Org\Tool\Arr;
use Org\Tool\Request;
use Org\Tool\Validation\Assert;
use function array_search;
use function is_array;

/**
 * Class Repository
 *
 * Find and build Models from the database.
 * Single Model or Collections.
 */
class Repository
{
    public const MAX_LIMIT = 10000;

    /**
     * Default columns for ->select() if none are passed.
     *
     * @const string[]
     */
    protected const DEFAULT_COLUMNS = ['*'];

    /**
     * @var BaseModel
     */
    protected $model;

    /**
     * Columns to get on current query call.
     *
     * @var array
     */
    protected $columns = self::DEFAULT_COLUMNS;

    /**
     * Use Select Raw?
     *
     * @var bool
     */
    protected $selectRaw = false;

    /**
     * @var string
     */
    protected $keyName;

    /**
     * "code" key to use.
     *
     * @var string
     */
    protected $code = 'code';

    /**
     * Repository constructor.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->model   = $model;
        $this->keyName = $model->getKeyName();
    }

    public function find($id): ?BaseModel
    {
        /** @var BaseModel|null $found */
        $found = $this->query()
            ->where($this->keyName, $id)
            ->first();

        return $found;
    }

    public function findLast(): ?BaseModel
    {
        /** @var BaseModel $found */
        $found = $this->query()
            ->latest()
            ->first();

        return $found;
    }

    public function findCodesAll(...$codes): \Org\Tool\Collection
    {
        $codes = \Org\Tool\Collection::make($codes)->castString()->all();

        return new \Org\Tool\Collection(
            $this->query()
                ->whereIn($this->code, $codes)
                ->get()
        );
    }

    public function findCode(string $code): ?BaseModel
    {
        /** @var BaseModel $found */
        $found = $this->query()
            ->where($this->code, trim($code))
            ->first();

        return $found;
    }

    public function findCodeOrNew(string $code): BaseModel
    {
        /** @var BaseModel $found */
        return $this->findCode($code) ??

            $this->model->makeInstance([
                $this->code => $code
            ]);
    }

    public function findCodeLoad(string $code, array $attributes): BaseModel
    {
        /** @var BaseModel $found */
        $found = $this->findCode($code) ?? $this->model->makeInstance();

        $attributes[$this->code] = $code;

        return $found->forceFill($attributes);
    }

    public function findOrNew($id, array $defaults): BaseModel
    {
        return static::find($id) ?? $this->model->makeInstance($defaults);
    }

    public function findOrCreate($id, array $defaults): BaseModel
    {
        $found = static::find($id);

        if ($found === null) {
            $found = $this->model->makeInstance($defaults);

            $save = $found->save();

            Assert::true($save, 'Model could not be found, and save failed.');
        }

        return $found;
    }

    public function findOrFail($id): BaseModel
    {
        $model = static::find($id);

        if ($model === null) {
            throw new ModelNotFound;
        }

        return $model;
    }

    public function findMany(array $ids): Collection
    {
        $found = $this->query()
            ->whereIn($this->keyName, $ids)
            ->get()
            ->keyBy($this->keyName);

        $results = [];

        // Set $id with result found or NULL if not found.
        foreach ($ids as $id) {
            $results[$id] = $found->get($id);
        }

        return new Collection($results);
    }

    public function findManyOrNew(array $ids, array $defaults): Collection
    {
        return static::findMany($ids)
            ->map(function ($result) use ($defaults) {

                return $result ?? $this->model->makeInstance($defaults);
            });
    }

    public function findManyOrFail(array $ids): Collection
    {
        $found = static::findMany($ids);

        // Find any ids that weren't found.
        $notFound = $found
            ->filter(function ($model) {

                return $model === null;
            })
            ->keys();

        if ($notFound->isNotEmpty()) {
            throw new ModelNotFound('Data not found with ids: ' . implode(', ', $notFound->all()));
        }

        return $found;
    }

    public function findRequest($id, string $formDot = 'form'): BaseModel
    {
        $attributes = (array) Request::findOrFail($formDot);

        return static::findOrFail($id)
            ->forceFill($attributes);
    }

    public function findInstance(): ?self
    {
        /** @var self $found */
        $found = $this->query()->first();

        return $found;
    }

    public function first(): ?BaseModel
    {
        return $this->query()->first();
    }

    /**
     * Delete all models that don't have these codes. Return number deleted.
     *
     * @param array $codes
     * @return int
     */
    public function deleteNotCodes(array $codes): int
    {
        return $this
            ->setSelect($this->code)
            ->query()
            ->whereNotIn($this->code, $codes)
            ->delete();
    }

    public function withCodes(...$codes): Builder
    {
        return $this->query()->whereIn($this->code, $codes);
    }

    /**
     * Return all, LIMIT enforced.
     *
     * @param string ...$columns
     * @return Builder
     */
    public function all(): Builder
    {
        return $this->query()
            ->limit(static::MAX_LIMIT)
            ->offset(0);
    }

    /**
     * Get all Model codes.
     *
     * @return string[]
     */
    public function allCodes(): array
    {
        return $this->setSelect($this->code)
            ->all()
            ->pluck($this->code)
            ->all();
    }

    public function replaceCode($code, array $attributes): BaseModel
    {
        return $this->findCodeLoad($code, $attributes)
            ->saveThis();
    }

    public function replaceWith(string $code, BaseModel $model): BaseModel
    {
        $found = $this->findCode($code) ?? $this->model->newInstance();

        // Replace existing with this one.
        $model->setPrimaryKey($found->getKey());
        $model->exists = $found->exists;

        return $model->saveThis();
    }

    public function replaceAll(string $code, BaseModel ...$models): Collection
    {
        return \Org\Tool\Collection::make($models)
            ->map(function (BaseModel $model) use ($code) {

                return $this->replaceWith($code, $model);
            });
    }

    /**
     * Set "code" column for code query methods.
     *
     * @param string $code
     * @return $this
     */
    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param string ...$columns
     * @return $this
     */
    public function setSelect(string ...$columns)
    {
        $this->selectRaw = false;
        $this->columns   = $columns;

        return $this;
    }

    /**
     * @param string ...$columns
     * @return $this
     */
    public function setSelectRaw(string ...$columns)
    {
        $this->setSelect(...$columns);
        $this->selectRaw = true;

        return $this;
    }

    /**
     * Get SELECT statement for current Query.
     *
     * @return array
     */
    public function getSelect(): array
    {
        $columns = $this->columns;

        if (is_array($columns) === false || $columns === []) {
            return static::DEFAULT_COLUMNS;
        }

        // Primary key isn't returned? - add it.
        if ($columns !== ['*'] && Arr::inLoose($columns, '*', $this->keyName) === false) {

            // Add to the front to debugging.
            $columns = Arr::prepend($columns, $this->keyName);
        }

        if ($columns !== ['*']) {
            unset($columns[array_search('*', $columns)]);
        }

        return $columns;
    }

    /**
     * Get query with columns and default LIMIT.
     *
     * @param string[] ...$columns
     * @return Builder
     */
    protected function query(): Builder
    {
        $query = $this->model
            ->newQuery()
            ->limit(static::MAX_LIMIT);

        if ($this->selectRaw) {
            $query->selectRaw(implode(', ', $this->getSelect()));
        }
        else {
            $query->select($this->getSelect());
        }

        return $query;
    }
}
