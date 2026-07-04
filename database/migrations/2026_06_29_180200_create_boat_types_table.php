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
        Schema::create('boat_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boatId');
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('priceReturnAdult', 10, 2)->default(0);
            $table->decimal('priceReturnChild', 10, 2)->default(0);
            $table->decimal('priceOneWayAdult', 10, 2)->default(0);
            $table->decimal('priceOneWayChild', 10, 2)->default(0);
            $table->string('currency', 10)->default('AUD');
            $table->string('childAgeNote')->nullable();
            $table->boolean('isActive')->default(true);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boat_types');
    }
};