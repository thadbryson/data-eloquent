<?php

declare(strict_types = 1);

namespace Data\Services;

use Data\BaseModel;
use Tool\Validation\Result;

class Validator
{
    /**
     * @var BaseModel
     */
    protected $model;

    /**
     * Data export of BaseModel - data for validation.
     *
     * @var array
     */
    protected $data;

    public function __construct(BaseModel $model)
    {
        $this->model = $model;
        $this->data  = $model->attributesToArray();
    }

    public function create(): Result
    {
        return Result::success();
    }

    public function update(): Result
    {
        return Result::success();
    }

    public function delete(): Result
    {
        return Result::success();
    }
}
