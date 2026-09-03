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
        Schema::create('property_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->string('channel')->default('primary');
            $table->string('language')->default('en');
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->longText('theSpace')->nullable();
            $table->longText('guestAccess')->nullable();
            $table->longText('theNeighborhood')->nullable();
            $table->longText('gettingAround')->nullable();
            $table->longText('otherThingsToNote')->nullable();

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_descriptions');
    }
};