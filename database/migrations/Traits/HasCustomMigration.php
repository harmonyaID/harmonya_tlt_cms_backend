<?php

namespace Database\Migrations\Traits;

use Illuminate\Database\Schema\Blueprint;

trait HasCustomMigration
{
    public function getDefaultTimestamps(Blueprint $table)
    {
        $table->timestamp('createdAt')->nullable();
        $table->timestamp('updatedAt')->nullable();
        $table->softDeletes('deletedAt');
    }

    public function getDefaultCreatedBy(Blueprint $table)
    {
        $table->bigInteger('createdBy')->nullable();
        $table->string('createdByName', 250)->nullable();
    }

    public function getDefaultUpdatedBy(Blueprint $table)
    {
        $table->bigInteger('updatedBy')->nullable();
        $table->string('updatedByName', 250)->nullable();
    }

    public function floatCustom(Blueprint $table, $column, $precision = 53, $scale = 2): \Illuminate\Database\Schema\ColumnDefinition
    {
        return $table->addColumn('float', $column, compact('precision', 'scale'));
    }
}
