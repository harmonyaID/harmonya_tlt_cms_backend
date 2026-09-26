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
        Schema::create('experience_tag', function (Blueprint $table) {
            $table->foreignId('experienceId');
            $table->foreignId('tagId');

            $table->unique(['experienceId', 'tagId']);

            $table->foreign('experienceId')
                ->references('id')
                ->on('experiences')
                ->cascadeOnDelete();

            $table->foreign('tagId')
                ->references('id')
                ->on('experience_tags')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_tag');
    }
};