<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->foreignId('featureId');
            $table->string('value')->nullable(); // e.g. "6" for Bedroom/Bathroom, null for WIFI, etc.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_feature');
    }
};
