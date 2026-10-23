<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Boat Transfer Booking Form redesign:
 * route (from / to), one departure + optional return (date + time),
 * phone with country code, land transport (pick up point, flight number,
 * arrival time), "booked through TLT" + property/booking info, surfboard, notes.
 *
 * Existing rows are converted before the old columns are dropped.
 */
return new class extends Migration
{
    private array $legacy = [
        'baliLandLocation',
        'departureDateFromBali', 'departureTimeFromBali', 'pickUpLocationBali',
        'hotelNameBali', 'hotelContactBali',
        'departureDateFromLembongan', 'departureTimeFromLembongan', 'dropOffLocationBali',
        'flightTime', 'hotelNameLembongan', 'accommodationLembongan',
        'passengerNames', 'hearAboutUs',
    ];

    public function up(): void
    {
        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->string('fromLocation')->nullable();
            $table->string('toLocation')->nullable();
            $table->date('departureDate')->nullable();
            $table->string('departureTime')->nullable();
            $table->date('returnDate')->nullable();
            $table->string('returnTime')->nullable();
            $table->string('phoneCountryCode', 10)->nullable();
            $table->boolean('needLandTransport')->default(false);
            $table->string('pickUpPoint')->nullable();
        });

        // keep what can be mapped from the old structure
        DB::table('boat_contact_forms')->orderBy('id')->each(function ($row) {
            $fromBali = !empty($row->departureDateFromBali);
            $fromLembongan = !empty($row->departureDateFromLembongan);

            $first = $fromBali
                ? [$row->departureDateFromBali, $row->departureTimeFromBali]
                : [$row->departureDateFromLembongan, $row->departureTimeFromLembongan];
            $second = $fromBali
                ? [$row->departureDateFromLembongan, $row->departureTimeFromLembongan]
                : [null, null];

            $isReturn = $row->ticketType === 'return';

            // free text -> airport | hotel_villa | not_applicable
            $pick = strtolower(trim((string) $row->pickUpLocationBali));
            $pickUpPoint = match (true) {
                $pick === '' => null,
                str_contains($pick, 'airport') => 'airport',
                str_contains($pick, 'hotel') || str_contains($pick, 'villa') => 'hotel_villa',
                default => 'not_applicable',
            };

            DB::table('boat_contact_forms')->where('id', $row->id)->update([
                'fromLocation' => $fromBali ? 'Bali' : ($fromLembongan ? 'Nusa Lembongan' : null),
                'toLocation' => $fromBali ? 'Nusa Lembongan' : ($fromLembongan ? 'Bali' : null),
                'departureDate' => $first[0],
                'departureTime' => $first[1],
                'returnDate' => $isReturn ? $second[0] : null,
                'returnTime' => $isReturn ? $second[1] : null,
                'pickUpPoint' => $pickUpPoint,
                'needLandTransport' => $pickUpPoint !== null,
            ]);
        });

        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->dropColumn($this->legacy);
        });
    }

    public function down(): void
    {
        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->string('baliLandLocation')->nullable();
            $table->date('departureDateFromBali')->nullable();
            $table->string('departureTimeFromBali')->nullable();
            $table->string('pickUpLocationBali')->nullable();
            $table->string('hotelNameBali')->nullable();
            $table->string('hotelContactBali')->nullable();
            $table->date('departureDateFromLembongan')->nullable();
            $table->string('departureTimeFromLembongan')->nullable();
            $table->string('dropOffLocationBali')->nullable();
            $table->string('flightTime')->nullable();
            $table->string('hotelNameLembongan')->nullable();
            $table->string('accommodationLembongan')->nullable();
            $table->text('passengerNames')->nullable();
            $table->string('hearAboutUs')->nullable();
        });

        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->dropColumn([
                'fromLocation', 'toLocation', 'departureDate', 'departureTime',
                'returnDate', 'returnTime', 'phoneCountryCode', 'needLandTransport', 'pickUpPoint',
            ]);
        });
    }
};
