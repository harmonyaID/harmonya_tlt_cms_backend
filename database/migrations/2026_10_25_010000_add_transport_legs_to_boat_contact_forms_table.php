<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Land transport is now described per leg (departure / return), pick up and drop off:
 *
 *  pickUpPoint   -> departurePickupPoint
 *  flightNumber  -> departurePickupFlightNumber
 *  arrivalTime   -> departurePickupArrivalTime
 *  + departureDropOff*, returnPickup*, returnDropOff*
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->renameColumn('pickUpPoint', 'departurePickupPoint');
            $table->renameColumn('flightNumber', 'departurePickupFlightNumber');
            $table->renameColumn('arrivalTime', 'departurePickupArrivalTime');
        });

        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->string('departureDropOffPoint')->nullable();
            $table->string('departureDropOffLocation')->nullable();
            $table->string('departureDropOffArrivalTime')->nullable();
            $table->string('returnPickupPoint')->nullable();
            $table->string('returnPickupLocation')->nullable();
            $table->string('returnPickupCheckoutTime')->nullable();
            $table->string('returnDropOffPoint')->nullable();
            $table->string('returnDropOffLocation')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->dropColumn([
                'departureDropOffPoint', 'departureDropOffLocation', 'departureDropOffArrivalTime',
                'returnPickupPoint', 'returnPickupLocation', 'returnPickupCheckoutTime',
                'returnDropOffPoint', 'returnDropOffLocation',
            ]);
        });

        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->renameColumn('departurePickupPoint', 'pickUpPoint');
            $table->renameColumn('departurePickupFlightNumber', 'flightNumber');
            $table->renameColumn('departurePickupArrivalTime', 'arrivalTime');
        });
    }
};
