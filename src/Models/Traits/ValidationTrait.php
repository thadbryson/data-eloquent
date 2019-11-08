<?php

declare(strict_types = 1);

namespace Data\Models\Traits;

use Data\BaseModel;
use Tool\Validation\Result;
use Tool\Validation\Validator;
use function array_replace_recursive;
use function property_exists;

/**
 * Trait ValidationTrait
 *
 * @property string[]|null $rules
 * @property string[]|null $messages
 * @property string[]|null $customAttributes
 *
 * @mixin BaseModel
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ValidationTrait
{
    protected static function bootValidationTrait(): void
    {
        self::saving(function ($model) {

            $model->validate()->assert();
        });
    }

    public function validate(array $rules = [], array $messages = [], array $customAttributes = []): Result
    {
        $data = $this->getAttributes();

        $rules = array_replace_recursive($this->rules(), $rules);

        // No rules, no validation?
        if ($rules === []) {
            return Result::success();
        }

        $messages         = array_replace_recursive($this->messages(), $messages);
        $customAttributes = array_replace_recursive($this->customAttributes(), $customAttributes);

        return Validator::validate($data, $rules, $messages, $customAttributes);
    }

    public function rules(): array
    {
        if (property_exists($this, 'rules') === false) {
            return [];
        }

        return $this->rules;
    }

    public function messages(): array
    {
        if (property_exists($this, 'messages') === false) {
            return [];
        }

        return $this->messages;
    }

    public function customAttributes(): array
    {
        if (property_exists($this, 'customAttributes') === false) {
            return [];
        }

        return $this->customAttributes;
    }
}
