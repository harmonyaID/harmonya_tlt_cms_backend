<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Booking quote shown to the guest on the Bookeasy booking form
 * (unit, price per night, fee, total, rate options, coupon), stored as sent.
 */
return new class extends Migration
{
    private array $columns = [
        'propertyVariantId', 'currency', 'pricePerNight', 'nights', 'fee', 'total',
        'payableAmount', 'payableCurrency', 'rateOptions', 'couponCode',
    ];

    public function up(): void
    {
        Schema::table('property_inquiry_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('property_inquiry_forms', 'propertyVariantId')) {
                $table->unsignedBigInteger('propertyVariantId')->nullable()->index()->after('propertyId');
            }
            if (!Schema::hasColumn('property_inquiry_forms', 'currency')) {
                $table->string('currency', 10)->nullable()->after('comments');
                $table->decimal('pricePerNight', 12, 2)->nullable()->after('currency');
                $table->unsignedInteger('nights')->nullable()->after('pricePerNight');
                $table->decimal('fee', 12, 2)->nullable()->after('nights');
                $table->decimal('total', 12, 2)->nullable()->after('fee');
                $table->decimal('payableAmount', 12, 2)->nullable()->after('total');
                $table->string('payableCurrency', 10)->nullable()->after('payableAmount');
                $table->json('rateOptions')->nullable()->after('payableCurrency');
                $table->string('couponCode', 100)->nullable()->after('rateOptions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('property_inquiry_forms', function (Blueprint $table) {
            foreach ($this->columns as $column) {
                if (Schema::hasColumn('property_inquiry_forms', $column)) {
                    if ($column === 'propertyVariantId') {
                        $table->dropIndex(['propertyVariantId']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
