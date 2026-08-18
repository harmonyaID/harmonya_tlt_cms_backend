<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * These fields now live on property_inquiry_forms - this table keeps
     * only what a general "Contact Us" style form needs.
     */
    public function up(): void
    {
        Schema::table('property_contact_forms', function (Blueprint $table) {
            $table->dropColumn([
                'checkInDate',
                'checkOutDate',
                'adultCount',
                'childCount',
                'infantCount',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_contact_forms', function (Blueprint $table) {
            $table->date('checkInDate')->nullable();
            $table->date('checkOutDate')->nullable();
            $table->unsignedInteger('adultCount')->default(1);
            $table->unsignedInteger('childCount')->default(0);
            $table->unsignedInteger('infantCount')->default(0);
        });
    }
};
