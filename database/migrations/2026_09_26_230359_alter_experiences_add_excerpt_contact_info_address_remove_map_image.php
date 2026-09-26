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
        Schema::table('experiences', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('name');
            $table->json('contactInfo')->nullable()->after('website');
            $table->string('address')->nullable()->after('mapLocationUrl');

            $table->dropColumn([
                'mapImage',
                'whatsapp',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'excerpt',
                'contactInfo',
                'address',
            ]);

            $table->string('mapImage')->nullable()->after('openHours');
            $table->string('whatsapp')->nullable()->after('mapLocationUrl');
        });
    }
};