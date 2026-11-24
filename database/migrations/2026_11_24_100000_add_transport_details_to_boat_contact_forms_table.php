<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'departurePickupLocation' => 'departurePickupPoint',
        'departureDropOffFlightNumber' => 'departureDropOffLocation',
        'returnPickupFlightNumber' => 'returnPickupLocation',
        'returnDropOffFlightNumber' => 'returnDropOffLocation',
    ];

    public function up(): void
    {
        Schema::table('boat_contact_forms', function (Blueprint $table) {
            foreach ($this->columns as $column => $after) {
                if (!Schema::hasColumn('boat_contact_forms', $column)) {
                    $table->string($column)->nullable()->after($after);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('boat_contact_forms', function (Blueprint $table) {
            foreach (array_keys($this->columns) as $column) {
                if (Schema::hasColumn('boat_contact_forms', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
