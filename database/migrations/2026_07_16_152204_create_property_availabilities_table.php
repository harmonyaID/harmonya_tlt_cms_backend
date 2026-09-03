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
        Schema::create('property_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId')->unique();
            $table->integer('defaultAvailabilityId')->nullable();
            $table->string('bookingWindow')->nullable();
            $table->integer('advanceNoticeValue')->nullable();
            $table->integer('advanceNoticeUnitId')->nullable();
            $table->integer('preparationTimeValue')->nullable();
            $table->text('checkInRestrictions')->nullable();
            $table->integer('maxNightsPerYear')->nullable();
            $table->integer('minLengthOfStay')->nullable();
            $table->integer('maxLengthOfStay')->nullable();

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_availabilities');
    }
};