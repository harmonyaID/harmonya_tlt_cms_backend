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
        Schema::create('experience_inquiry_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experienceId');
            $table->string('fullName');
            $table->string('phone');
            $table->string('email');
            $table->date('eventDate')->nullable();
            $table->integer('totalGuests')->nullable();
            $table->string('countryOfResidence')->nullable();
            $table->string('mealStyle')->nullable();
            $table->string('weddingLocation')->nullable();  // Private Villa, Hotel, etc
            $table->string('ceremonyType')->nullable();     // Legal Marriage, etc
            $table->integer('accommodationNights')->nullable();
            $table->integer('maxNightlyBudget')->nullable();
            $table->text('notes')->nullable();

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_inquiry_forms');
    }
};