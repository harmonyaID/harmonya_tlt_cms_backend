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
        Schema::create('property_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId')->unique();
            $table->decimal('weekdayBasePrice', 12, 2)->nullable();
            $table->decimal('weekendBasePrice', 12, 2)->nullable();
            $table->string('rateStrategy')->nullable();
            $table->decimal('cleaningFee', 12, 2)->nullable();
            $table->integer('cleaningFeeTypeId')->nullable();
            $table->decimal('extraPersonFee', 12, 2)->nullable();
            $table->decimal('securityDepositFee', 12, 2)->nullable();
            $table->decimal('weeklyDiscount', 5, 2)->default(0);
            $table->decimal('monthlyDiscount', 5, 2)->default(0);
            $table->decimal('markupPercent', 5, 2)->default(0);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_pricings');
    }
};