<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blogId');
            $table->foreignId('tagId');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tag');
    }
};