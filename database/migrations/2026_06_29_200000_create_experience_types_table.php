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
        Schema::create('experience_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_types');
    }
};