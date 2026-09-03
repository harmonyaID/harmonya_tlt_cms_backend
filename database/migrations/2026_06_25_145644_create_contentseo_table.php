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
        Schema::create('contentseo', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('contentableId');
            $table->string('contentableType');

            $table->string('info')->nullable();

            $table->string('title');
            $table->string('slug')->nullable();

            $table->text('description')->nullable();
            $table->text('metaKeyword')->nullable();

            $table->string('thumbnail')->nullable();
            $table->string('canonicalUrl')->nullable();

            $table->boolean('robotIndex')->default(true);
            $table->boolean('robotFollow')->default(true);

            $table->index(['contentableId', 'contentableType']);

            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contentseo');
    }
};
