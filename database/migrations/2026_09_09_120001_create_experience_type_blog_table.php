<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('experience_type_blog', function(Blueprint $table){ $table->id(); $table->foreignId('experienceTypeId')->constrained('experience_types')->cascadeOnDelete(); $table->foreignId('blogId')->constrained('blogs')->cascadeOnDelete(); $table->unique(['experienceTypeId','blogId']); }); } public function down(): void { Schema::dropIfExists('experience_type_blog'); } };
