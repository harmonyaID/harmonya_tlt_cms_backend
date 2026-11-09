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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->string('value', 250);
            $table->text('description')->nullable();

            $this->getDefaultTimestamps($table);
            $this->getDefaultUpdatedBy($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
