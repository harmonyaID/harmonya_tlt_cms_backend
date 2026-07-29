<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('locale')->nullable()->default('en')->after('id');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->string('locale')->nullable()->default('en')->after('id');
        });

        Schema::table('boats', function (Blueprint $table) {
            $table->string('locale')->nullable()->default('en')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('boats', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};