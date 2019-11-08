<?php

declare(strict_types = 1);

namespace Tests\Support\Stubs;

use Data\BaseModel;

/**
 * Class UserModel
 */
class UserModel extends BaseModel
{
    protected $table = 'users';

    public $timestamps = false;
}