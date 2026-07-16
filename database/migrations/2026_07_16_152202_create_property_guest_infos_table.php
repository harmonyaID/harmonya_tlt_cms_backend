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
        Schema::create('property_guest_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId')->unique();
            $table->string('hostName')->nullable();
            $table->string('wifiName')->nullable();
            $table->string('wifiPassword')->nullable();
            $table->text('houseManual')->nullable();
            $table->text('trashInstructions')->nullable();
            $table->text('parkingInstructions')->nullable();
            $table->text('cleaningInstructions')->nullable();
            $table->text('interactionWithGuests')->nullable();

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_guest_infos');
    }
};