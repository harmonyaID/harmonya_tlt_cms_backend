<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('blog_property', function(Blueprint $table){ $table->id(); $table->foreignId('blogId')->constrained('blogs')->cascadeOnDelete(); $table->foreignId('propertyId')->constrained('properties')->cascadeOnDelete(); $table->unsignedInteger('order')->default(0); $table->unique(['blogId','propertyId']); }); } public function down(): void { Schema::dropIfExists('blog_property'); } };
