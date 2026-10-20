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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('guestyId')->nullable()->unique();
            $table->timestamp('guestyImportedAt')->nullable();
            $table->integer('sourceTypeId')->nullable();
            $table->foreignId('propertyTypeId')->nullable();
            $table->string('nickname');
            $table->integer('unitTypeId');
            $table->integer('listingTypeId');
            $table->string('roomType')->nullable();
            $table->integer('occupancy')->nullable();
            $table->decimal('propertySize', 10, 2)->nullable();
            $table->integer('statusId');
            $table->boolean('isPopular')->default(false);
            $table->integer('cleaningStatusId')->nullable();
            $table->string('currency')->default('USD');
            $table->string('floorplanImage', 500)->nullable();
            $table->json('whyYoullLoveThis')->nullable();
            $table->dateTime('sourceCreatedAt')->nullable();
            $table->dateTime('sourceUpdatedAt')->nullable();

            $this->getDefaultTimestamps($table);
        });

        Schema::create('property_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->integer('typeId');
            $table->string('address')->nullable();
            $table->string('buildingName')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('zipCode')->nullable();

            $this->getDefaultTimestamps($table);
        });

        Schema::create('property_guest_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId')->unique();
            $table->string('hostName')->nullable();
            $table->string('wifiName')->nullable();
            $table->string('wifiPassword')->nullable();
            $table->longText('houseManual')->nullable();
            $table->longText('trashInstructions')->nullable();
            $table->longText('parkingInstructions')->nullable();
            $table->longText('cleaningInstructions')->nullable();
            $table->longText('interactionWithGuests')->nullable();

            $this->getDefaultTimestamps($table);
        });

        Schema::create('property_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->foreignId('roomTypeId');
            $table->string('label')->nullable();
            $table->foreignId('bedTypeId')->nullable();
            $table->integer('bedCount')->nullable();
            $table->integer('order')->default(0);

            $this->getDefaultTimestamps($table);
        });

        Schema::create('property_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId')->unique();
            $table->integer('defaultAvailabilityId')->nullable();
            $table->string('bookingWindow')->nullable();
            $table->integer('advanceNoticeValue')->nullable();
            $table->integer('advanceNoticeUnitId')->nullable();
            $table->integer('preparationTimeValue')->nullable();
            $table->longText('checkInRestrictions')->nullable();
            $table->integer('maxNightsPerYear')->nullable();
            $table->integer('minLengthOfStay')->nullable();
            $table->integer('maxLengthOfStay')->nullable();

            $this->getDefaultTimestamps($table);
        });

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

        Schema::create('property_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->string('channel')->default('primary');
            $table->string('language')->default('en');
            $table->string('title')->nullable();
            $table->longText('summary')->nullable();
            $table->longText('theSpace')->nullable();
            $table->longText('guestAccess')->nullable();
            $table->longText('theNeighborhood')->nullable();
            $table->longText('gettingAround')->nullable();
            $table->longText('otherThingsToNote')->nullable();

            $this->getDefaultTimestamps($table);
        });

        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);

            $this->getDefaultTimestamps($table);
        });

        Schema::create('property_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->foreignId('amenityId');
        });

        Schema::create('property_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->foreignId('tagId');
        });

        Schema::create('property_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->foreignId('featureId');
            $table->string('value')->nullable();
        });

        Schema::create('property_related', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->foreignId('relatedPropertyId');
            $table->integer('order')->default(0);
        });

        Schema::create('property_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propertyId');
            $table->string('name');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('review');
            $table->boolean('isActive')->default(true);

            $this->getDefaultTimestamps($table);
        });

        Schema::create('property_review_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewId');
            $table->string('photo');

            $this->getDefaultTimestamps($table);
        });

        // A variant belongs to (at most) one parent Property.
        Schema::table('property_variants', function (Blueprint $table) {
            $table->foreignId('parentPropertyId')
                ->nullable()
                ->after('id')
                ->constrained('properties')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('property_variants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parentPropertyId');
        });

        Schema::dropIfExists('property_review_photos');
        Schema::dropIfExists('property_reviews');
        Schema::dropIfExists('property_related');
        Schema::dropIfExists('property_feature');
        Schema::dropIfExists('property_tag');
        Schema::dropIfExists('property_amenity');
        Schema::dropIfExists('property_photos');
        Schema::dropIfExists('property_descriptions');
        Schema::dropIfExists('property_pricings');
        Schema::dropIfExists('property_availabilities');
        Schema::dropIfExists('property_rooms');
        Schema::dropIfExists('property_guest_infos');
        Schema::dropIfExists('property_addresses');
        Schema::dropIfExists('properties');
    }
};
