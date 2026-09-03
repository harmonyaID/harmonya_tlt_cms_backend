<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasCustomMigration;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoryId')->nullable();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->boolean('isPopular')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('isPublish')->default(true);

            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};