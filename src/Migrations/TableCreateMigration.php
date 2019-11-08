<?php

declare(strict_types = 1);

namespace Data\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class TableCreateMigration extends Migration
{
    use HelperMigrationTrait;

    protected const SIZE_NORMAL = 50;

    protected const SIZE_MEDIUM = 255;

    protected const SIZE_LARGE = 1000;

    protected const SIZE_XL = 2000;

    protected const SIZE_2X = 4000;

    protected $id = true;

    protected $uuid = false;

    protected $foreignIds = [];

    /**
     * @var string[]
     */
    protected $updateCascades;

    /**
     * @var string[]
     */
    protected $deleteCascades;

    /**
     * "name" column with ->nameSize size.
     *
     * @var bool
     */
    protected $name = false;

    /**
     * Size of the "name" column.
     *
     * @var int
     */
    protected $nameSize = self::SIZE_NORMAL;

    protected $code = false;

    protected $codeSize = self::SIZE_NORMAL;

    protected $description = false;

    protected $descriptionSize = self::SIZE_MEDIUM;

    /**
     * Create a "meta" column with JSON data.
     *
     * @var bool
     */
    protected $meta = false;

    /**
     * Datetime columns to create. Default NULL.
     *
     * @var string[]
     */
    protected $datetimes = [];

    protected $timestamps = true;

    protected $softDeletes = false;

    /**
     * Additional columns to create.
     *
     * @param Blueprint $table
     */
    abstract protected function columns(Blueprint $table): void;

    /**
     * Name of table to create
     *
     * @return string
     */
    abstract protected function table(): string;

    public function up(): void
    {
        Schema::create($this->table(), function (Blueprint $table) {

            if ($this->id && $this->uuid === false) {
                $this->addId($table);
            }

            if ($this->uuid) {
                $this->addUuid($table);
            }

            foreach ($this->foreignIds as $keyName => $tableName) {
                $this->addForeignId($table, $keyName);
            }

            if ($this->code) {
                $this->addCode($table, $this->codeSize);
            }

            if ($this->name) {
                $this->addName($table, $this->nameSize);
            }

            if ($this->description) {
                $this->addDescription($table, $this->descriptionSize);
            }

            if ($this->meta) {
                $this->addMeta($table);
            }

            $this->columns($table);

            foreach ($this->datetimes as $datetimeColumn) {
                $this->addDateTime($table, $datetimeColumn);
            }

            if ($this->timestamps) {
                $table->timestamps();
            }

            if ($this->softDeletes) {
                $table->softDeletes();
            }
        });

        if ($this->foreignIds !== []) {

            Schema::table($this->table(), function (Blueprint $table) {

                $updateCascades = $this->updateCascades ?? array_keys($this->foreignIds);
                $deleteCascades = $this->deleteCascades ?? array_keys($this->foreignIds);

                foreach ($this->foreignIds as $keyName => $foreignTable) {

                    $foreign = $table->foreign($keyName)
                        ->on($foreignTable)
                        ->references('id');

                    if (in_array($keyName, $updateCascades)) {
                        $foreign->onUpdate('cascade');
                    }

                    if (in_array($keyName, $deleteCascades)) {
                        $foreign->onDelete('cascade');
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists($this->table());

        Schema::enableForeignKeyConstraints();
    }
}