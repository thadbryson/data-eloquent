<?php

declare(strict_types = 1);

namespace Data\Migrations\Api;

use Data\Migrations\HelperMigrationTrait;
use Data\Migrations\TableCreateMigration;
use Illuminate\Database\Schema\Blueprint;

abstract class Oauth2 extends TableCreateMigration
{
    use HelperMigrationTrait;

    protected $code = true;

    protected function table(): string
    {
        return 'oauth2';
    }

    protected function columns(Blueprint $table): void
    {
        $table->integer('expires_in')->nullable()->default(null);
        $table->dateTime('expires_at');
        $table->string('token');
        $table->string('token_refresh');
    }
}
