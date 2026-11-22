<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * An offer can have several categories (like blogs). offers.categoryId is kept
 * and holds the first category, for existing clients.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('offer_category')) {
            Schema::create('offer_category', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('offerId')->index();
                $table->unsignedBigInteger('categoryId')->index();
                $table->unique(['offerId', 'categoryId']);
            });
        }

        // existing offers keep their category
        DB::table('offers')
            ->whereNotNull('categoryId')
            ->orderBy('id')
            ->each(function ($offer) {
                DB::table('offer_category')->insertOrIgnore([
                    'offerId' => $offer->id,
                    'categoryId' => $offer->categoryId,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_category');
    }
};
