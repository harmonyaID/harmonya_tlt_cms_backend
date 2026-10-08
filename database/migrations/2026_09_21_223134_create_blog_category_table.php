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
        Schema::create('blog_category', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blogId')
                ->constrained('blogs')
                ->cascadeOnDelete();

            $table->foreignId('categoryId')
                ->constrained('blog_categories')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'blogId',
                'categoryId',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_category');
    }
};
