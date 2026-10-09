<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('island_guide_types', function (Blueprint $table) {
            $table->string('excerpt', 500)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('island_guide_types', function (Blueprint $table) {
            $table->dropColumn('excerpt');
        });
    }
};
