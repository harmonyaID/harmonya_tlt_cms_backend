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
        Schema::create('property_contact_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('statusId')->default(MailStatus::MAIL_STATUS_PENDING_ID);
            $table->foreignId('propertyId')->nullable();
            $table->integer('sourceTypeId')->nullable(); // App\Services\Constant\Property\PropertySourceType (Guesty/Bookeasy), null = direct website

            // Customer Info
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Booking Detail
            $table->date('checkInDate')->nullable();
            $table->date('checkOutDate')->nullable();
            $table->unsignedInteger('adultCount')->default(1);
            $table->unsignedInteger('childCount')->default(0);
            $table->unsignedInteger('infantCount')->default(0);
            $table->text('message')->nullable();

            $table->boolean('isRead')->default(false);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_contact_forms');
    }
};
