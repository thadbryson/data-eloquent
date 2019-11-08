<?php

declare(strict_types = 1);

namespace Data\Migrations\Cms;

use Data\Migrations\HelperMigrationTrait;
use Data\Migrations\TableCreateMigration;
use Illuminate\Database\Schema\Blueprint;

abstract class Menu extends TableCreateMigration
{
    use HelperMigrationTrait;

    protected $name = true;

    protected $code = true;

    protected function table(): string
    {
        return 'cms_nav_menus';
    }

    protected function columns(Blueprint $table): void
    {
    }
}
