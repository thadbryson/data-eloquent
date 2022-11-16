<?php

declare(strict_types = 1);

namespace Data\Services;

use Data\BaseModel;
use Org\Validation\Result;

class Validator
{
    protected Result $result;

    public function __construct(BaseModel $model, array $rules, array $messages, array $customAttributes)
    {
        $this->result = \Org\Validation\Validator::validate(
            $model->attributesToArray(),
            $rules,
            $messages,
            $customAttributes
        );
    }

    public function getResult(): Result
    {
        return $this->result;
    }
}
