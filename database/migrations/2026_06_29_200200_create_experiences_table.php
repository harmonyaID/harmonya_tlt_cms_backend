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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experienceTypeId');
            $table->foreignId('experienceCategoryId')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('catalogPdf')->nullable();
            $table->string('openHours')->nullable();
            $table->string('mapEmbedUrl')->nullable();
            $table->string('mapLocationUrl')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('website')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('showInquiry')->default(false);
            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};