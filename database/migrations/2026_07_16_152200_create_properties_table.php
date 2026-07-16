<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasCustomMigration;

    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyTypeId')->nullable();
            $table->string('nickname');
            $table->integer('unitTypeId');
            $table->integer('listingTypeId');
            $table->string('roomType')->nullable();
            $table->integer('occupancy')->nullable();
            $table->decimal('propertySize', 10, 2)->nullable();
            $table->integer('statusId');
            $table->integer('cleaningStatusId')->nullable();
            $table->string('currency')->default('USD');

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};