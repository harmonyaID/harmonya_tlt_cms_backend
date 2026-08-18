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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable(); // rich text editor - longText from the start
            $table->timestamp('startDate')->nullable();
            $table->timestamp('endDate')->nullable();
            $table->timestamp('publishedAt')->nullable();
            $table->string('locale')->nullable()->default('en');
            $table->boolean('isActive')->default(true);

            $this->getDefaultTimestamps($table);
        });

        Schema::create('offer_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerId');
            $table->foreignId('propertyId');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_property');
        Schema::dropIfExists('offers');
    }
};
