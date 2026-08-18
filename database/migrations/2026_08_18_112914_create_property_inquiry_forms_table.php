<?php

use App\Services\Constant\Global\MailStatus;
use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasCustomMigration;

    public function up(): void
    {
        Schema::create('property_inquiry_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('statusId')->default(MailStatus::MAIL_STATUS_PENDING_ID);
            $table->foreignId('propertyId');
            $table->integer('sourceTypeId')->nullable(); // App\Models\Property\PropertySourceType (Guesty/Bookeasy), null = direct website

            // Customer Info
            $table->string('name');
            $table->string('email');
            $table->string('countryCode'); // e.g. "+62"
            $table->string('mobileNumber');

            // Dates
            $table->date('checkInDate')->nullable();
            $table->date('checkOutDate')->nullable();
            $table->boolean('isDatesFlexible')->default(false);
            $table->string('flexibleMonth')->nullable(); // e.g. "December" - used when isDatesFlexible = true
            $table->unsignedSmallInteger('flexibleYear')->nullable();

            // Guests
            $table->unsignedInteger('adultCount')->default(1);
            $table->json('childrenAges')->nullable(); // e.g. [5, 8, 12] - one entry per child, per BookEasy's "number of children + age"

            $table->text('comments')->nullable();

            $table->boolean('isRead')->default(false);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_inquiry_forms');
    }
};
