<?php

namespace Database\Migrations\Traits;

use Illuminate\Database\Schema\Blueprint;
use function Laravel\Prompts\table;

trait HasCustomMigration
{
    /**
     * @param Blueprint $table
     *
     * @return void
     */
    public function getDefaultTimestamps(Blueprint $table)
    {
        $table->timestamp('createdAt')->nullable();
        $table->timestamp('updatedAt')->nullable();
        $table->softDeletes('deletedAt');
    }

    /**
     * @param Blueprint $table
     *
     * @return void
     */
    public function getDefaultCreatedBy(Blueprint $table)
    {
        $table->bigInteger('createdBy')->nullable();
        $table->string('createdByName', 250)->nullable();
    }

    /**
     * @param Blueprint $table
     *
     * @return void
     */
    public function getDefaultUpdatedBy(Blueprint $table)
    {
        $table->bigInteger('updatedBy')->nullable();
        $table->string('updatedByName', 250)->nullable();
    }

    /**
     * @param Blueprint $table
     * @param $column
     * @param $precision
     * @param $scale
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function floatCustom(Blueprint $table, $column, $precision = 53, $scale = 2): \Illuminate\Database\Schema\ColumnDefinition
    {
        return $table->addColumn('float', $column, compact('precision', 'scale'));
    }

}
