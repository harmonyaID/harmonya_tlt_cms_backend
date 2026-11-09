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
        Schema::create('offer_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $this->getDefaultTimestamps($table);
        });

        Schema::create('offer_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerId');
            $table->foreignId('tagId');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('categoryId')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('categoryId');
        });

        Schema::dropIfExists('offer_tag');
        Schema::dropIfExists('offer_tags');
    }
};
