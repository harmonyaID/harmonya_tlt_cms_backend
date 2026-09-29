<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->json('priceFiles')->nullable()->after('promoPhotos');
            $table->dropColumn('priceFile');
        });
    }

    public function down(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->string('priceFile')->nullable()->after('promoPhotos');
            $table->dropColumn('priceFiles');
        });
    }
};