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
        Schema::create('boats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('routeFrom')->nullable();
            $table->string('routeTo')->nullable();
            $table->json('departureTimesFromBali')->nullable();
            $table->json('departureTimesFromLembongan')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('discountPercentage')->default(0);
            $table->boolean('isActive')->default(true);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boats');
    }
};