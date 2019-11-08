<?php

declare(strict_types = 1);

namespace Data\Exceptions;

use Throwable;

class ModelNotFound extends \Illuminate\Database\Eloquent\ModelNotFoundException
{
    public function __construct(string $message = 'Data could not be found.', int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}