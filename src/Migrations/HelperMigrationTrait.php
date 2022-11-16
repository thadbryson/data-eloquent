<?php

declare(strict_types = 1);

namespace Data\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;
use Org\Tool\Str;

/**
 * Trait HelperMigrationTrait
 */
trait HelperMigrationTrait
{
    protected function addId(Blueprint $table)
    {
        return $table->bigIncrements('id');
    }

    protected function addUuid(Blueprint $table)
    {
        return $table->char('id', $length = 36)->index();
    }

    protected function addForeignId(Blueprint $table, string $column)
    {
        $column = Str::make($column)->ensureRight('_id')->get();

        return $table->bigInteger($column)->unsigned();
    }

    protected function addName(Blueprint $table, int $size = self::SIZE_NORMAL, string $column = 'name')
    {
        return $table->string($column, $size);
    }

    protected function addCode(Blueprint $table, int $size = self::SIZE_NORMAL, string $column = 'code')
    {
        return $table->string($column, $size)
            ->index()
            ->unique();
    }

    protected function addDescription(Blueprint $table, int $size = self::SIZE_MEDIUM, string $column = 'description')
    {
        return $table->string($column, $size)->default(null)->nullable();
    }

    protected function addMeta(Blueprint $table, string $column = 'meta')
    {
        return $table->json($column);
    }

    protected function addIdentifier(Blueprint $table, string $column, bool $index = true): Fluent
    {
        $column = Str::make($column)->ensureRight('_id')->get();

        $column = $table->bigInteger($column)->unsigned();

        if ($index) {
            $column->index();
        }

        return $column;
    }

    protected function addBoolean(Blueprint $table, string $column, bool $default = null): Fluent
    {
        $column = Str::make($column)->ensureLeft('is_')->get();

        return $table->boolean($column)
            ->default($default);
    }

    protected function addTime(Blueprint $table, string $column): Fluent
    {
        $column = Str::make($column)->ensureRight('_at')->get();

        return $table->time($column);
    }

    protected function addDateTime(Blueprint $table, string $column, bool $defaultNull = true): Fluent
    {
        $column = Str::make($column)->ensureRight('_at')->get();

        $column = $table->dateTime($column);

        if ($defaultNull) {
            $column->nullable()->default(null);
        }

        return $column;
    }

    protected function addCountry(Blueprint $table, string $column = 'country'): Fluent
    {
        return $table->string($column, 3);
    }

    protected function addCountryName(Blueprint $table, string $column = 'country_name'): Fluent
    {
        return $table->string($column, 100);
    }

    protected function addAddress(Blueprint $table, string $column = 'address'): Fluent
    {
        return $table->string($column, 255);
    }

    protected function addAddress2(Blueprint $table, string $column = 'address_2'): Fluent
    {
        return $table->string($column, 20);
    }

    protected function addCity(Blueprint $table, string $column = 'city'): Fluent
    {
        return $table->string($column, 100);
    }

    protected function addState(Blueprint $table, string $column = 'state'): Fluent
    {
        return $table->string($column, 3);
    }

    protected function addStateName(Blueprint $table, string $column = 'state_name'): Fluent
    {
        return $table->string($column, 100);
    }

    protected function addZipCode(Blueprint $table, string $column = 'zip_code'): Fluent
    {
        return $table->string($column, 20);
    }

    protected function addLatitude(Blueprint $table, string $column = 'latitude', int $decimals = 6): Fluent
    {
        return $table->decimal($column, $decimals + 3, $decimals);
    }

    protected function addLongitude(Blueprint $table, string $column = 'longitude', int $decimals = 6): Fluent
    {
        return $table->decimal($column, $decimals + 3, $decimals);
    }

    protected function addPhone(Blueprint $table, string $column = 'phone'): Fluent
    {
        return $table->string($column, 50);
    }

    protected function addFax(Blueprint $table, string $column = 'fax'): Fluent
    {
        return $table->string($column, 50);
    }

    protected function addEmail(Blueprint $table, string $column = 'email'): Fluent
    {
        return $table->string($column, 320);
    }

    protected function addPassword(Blueprint $table, string $column = 'password'): Fluent
    {
        return $table->string($column, 300);
    }

    protected function addToken(Blueprint $table, string $column = 'token'): Fluent
    {
        return $table->string('api_token')
            ->default('')
            ->nullable(false)
            ->unique();
    }

    protected function addLink(Blueprint $table, string $column = 'link'): ColumnDefinition
    {
        return $table->string($column, 1024);
    }

    protected function addIpAddress(Blueprint $table, string $column = 'ip_address'): Fluent
    {
        return $table->string($column, self::SIZE_NORMAL);
    }

    protected function addTimezone(Blueprint $table, string $column = 'timezone'): Fluent
    {
        return $table->enum($column, \DateTimeZone::listIdentifiers());
    }

    protected function addForeignToSelf(string $localTable, string $localColumn, string $foreignColumn = 'id'): Fluent
    {
        $foreign = null;

        Schema::table($localTable, function (Blueprint $table) use (&$foreign, $localTable, $localColumn, $foreignColumn) {

            $foreign = $table->foreign($localColumn)
                ->references($foreignColumn)
                ->on($localTable);
        });

        return $foreign;
    }

    protected function addForeign(string $localTable, string $localColumn, string $foreignTable, string $foreignColumn = 'id'): Fluent
    {
        $foreign = null;

        Schema::table($localTable, function (Blueprint $table) use (&$foreign, $localTable, $localColumn, $foreignTable, $foreignColumn) {

            if (Schema::hasColumn($localTable, $localColumn) === false) {
                $table->bigInteger($localColumn)
                    ->unsigned()
                    ->after('id');
            }

            $foreign = $table->foreign($localColumn)
                ->references($foreignColumn)
                ->on($foreignTable)
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        return $foreign;
    }

    protected function addForeignNoAction(string $localTable, string $localColumn, string $foreignTable, string $foreignColumn = 'id'): Fluent
    {
        $foreign = null;

        Schema::table($localTable, function (Blueprint $table) use (&$foreign, $localColumn, $foreignTable, $foreignColumn) {

            $foreign = $table->foreign($localColumn)
                ->references($foreignColumn)
                ->on($foreignTable);
        });

        return $foreign;
    }
}
