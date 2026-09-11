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
        Schema::create('island_guide_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('islandGuideTypeId');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('featuredImage')->nullable();
            $table->string('banner')->nullable();
            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_guide_areas');
    }
};
