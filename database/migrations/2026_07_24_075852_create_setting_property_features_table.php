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
        Schema::create('setting_property_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->boolean('hasValue')->default(false); // e.g. "Bedroom" needs a number (Up to X), "WIFI" doesn't
            $table->integer('order')->default(0);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_property_features');
    }
};
