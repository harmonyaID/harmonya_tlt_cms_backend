<?php

use App\Services\Constant\Global\MailStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('website_contact_forms', function (Blueprint $table) {
            $table->unsignedTinyInteger('statusId')
                ->default(MailStatus::MAIL_STATUS_PENDING_ID)
                ->after('id');
        });

        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->unsignedTinyInteger('statusId')
                ->default(MailStatus::MAIL_STATUS_PENDING_ID)
                ->after('id');
        });

        Schema::table('experience_inquiry_forms', function (Blueprint $table) {
            $table->unsignedTinyInteger('statusId')
                ->default(MailStatus::MAIL_STATUS_PENDING_ID)
                ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_contact_forms', function (Blueprint $table) {
            $table->dropColumn('statusId');
        });

        Schema::table('boat_contact_forms', function (Blueprint $table) {
            $table->dropColumn('statusId');
        });

        Schema::table('experience_inquiry_forms', function (Blueprint $table) {
            $table->dropColumn('statusId');
        });
    }
};