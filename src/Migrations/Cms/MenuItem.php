<?php

declare(strict_types = 1);

namespace Data\Migrations\Cms;

use Data\Migrations\HelperMigrationTrait;
use Data\Migrations\TableCreateMigration;
use Illuminate\Database\Schema\Blueprint;

abstract class MenuItem extends TableCreateMigration
{
    use HelperMigrationTrait;

    protected $foreignIds = [
        'menu_id' => 'cms_nav_menus'
    ];

    protected $name = true;

    protected function table(): string
    {
        return 'cms_nav_items';
    }

    protected function columns(Blueprint $table): void
    {
        $table->integer('position');
        $this->addLink($table)->nullable();
        $this->addBoolean($table, 'is_external', true);
    }
}
