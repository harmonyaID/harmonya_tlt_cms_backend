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
        Schema::create('property_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->foreignId('roomTypeId');
            $table->string('label')->nullable();
            $table->foreignId('bedTypeId')->nullable();
            $table->integer('bedCount')->nullable();
            $table->integer('order')->default(0);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_rooms');
    }
};