<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->text('mapLocationUrl')->nullable()->change();
        });
        Schema::table('island_guides', function (Blueprint $table) {
            $table->text('mapLocationUrl')->nullable()->change();
        });
    }
    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('mapLocationUrl')->nullable()->change();
        });
        Schema::table('island_guides', function (Blueprint $table) {
            $table->string('mapLocationUrl')->nullable()->change();
        });
    }
};
