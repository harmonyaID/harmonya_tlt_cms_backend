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
        Schema::create('island_guides', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->nullable()->default('en');
            $table->foreignId('islandGuideTypeId');
            $table->foreignId('islandGuideAreaId')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('openHours')->nullable();
            $table->string('mapImage')->nullable();
            $table->string('mapLocationUrl')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('website')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('showInquiry')->default(false);
            $table->json('catalogs')->nullable();
            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_guides');
    }
};
