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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 256);
            $table->text('shortDescription');
            $table->longText('content');
            $table->string('status', 100);
            $table->string('template', 100)->nullable();
            $table->text('featuredImage')->nullable();
            $table->foreignId('createdBy')->nullable();
            $table->string('galleryType')->nullable();
            $table->integer('groupId')->nullable();
            $table->string('locale')->nullable();
            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
