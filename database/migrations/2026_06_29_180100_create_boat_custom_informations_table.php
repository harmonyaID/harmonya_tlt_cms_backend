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
        Schema::create('boat_custom_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boatId');
            $table->string('name');
            $table->string('value');
            $table->integer('order')->default(0);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boat_custom_informations');
    }
};