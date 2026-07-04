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
        Schema::create('boat_contact_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boatId')->nullable();
            $table->foreignId('boatTypeId')->nullable();

            // Customer Info
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Booking Detail
            $table->string('ticketType')->default('one_way');          // one_way / return
            $table->string('baliLandLocation')->nullable();
            $table->boolean('bookedThroughTlt')->default(false);
            $table->string('tltBookingRefName')->nullable();
            $table->unsignedInteger('adultCount')->default(1);
            $table->unsignedInteger('childCount')->default(0);         // 3-10 years
            $table->unsignedInteger('infantCount')->default(0);        // 0-2 years

            // From Bali
            $table->date('departureDateFromBali')->nullable();
            $table->string('departureTimeFromBali')->nullable();
            $table->string('pickUpLocationBali')->nullable();
            $table->string('flightNumber')->nullable();
            $table->string('arrivalTime')->nullable();
            $table->string('hotelNameBali')->nullable();
            $table->string('hotelContactBali')->nullable();

            // From Lembongan
            $table->date('departureDateFromLembongan')->nullable();
            $table->string('departureTimeFromLembongan')->nullable();
            $table->string('dropOffLocationBali')->nullable();
            $table->string('flightTime')->nullable();
            $table->string('hotelNameLembongan')->nullable();
            $table->string('accommodationLembongan')->nullable();

            // Passengers & Extra
            $table->text('passengerNames')->nullable();
            $table->boolean('hasSurfboard')->default(false);
            $table->string('hearAboutUs')->nullable();
            $table->text('message')->nullable();

            // Status
            $table->boolean('isRead')->default(false);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boat_contact_forms');
    }
};