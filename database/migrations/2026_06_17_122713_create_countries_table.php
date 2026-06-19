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
        Schema::create('setting_countries', function (Blueprint $table) {
            $table->id();
            $table->string('cca2', 2);
            $table->string('cca3', 3)->nullable();
            $table->string('ccn3')->nullable();
            $table->string('name');
            $table->string('phoneCode')->nullable();
            $table->string('flag')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isPopular')->default(false);

            $this->getDefaultCreatedBy($table);
            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_countries');
    }
};
