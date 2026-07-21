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
        Schema::create('lembongan_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('featuredImage')->nullable();
            $table->string('url')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('isActive')->default(true);

            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembongan_areas');
    }
};
