<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_analytics', function (Blueprint $table) {
            $table->renameColumn('value', 'head');
        });

        Schema::table('setting_analytics', function (Blueprint $table) {
            $table->text('body')->nullable()->after('head');
        });
    }

    public function down(): void
    {
        Schema::table('setting_analytics', function (Blueprint $table) {
            $table->dropColumn('body');
        });

        Schema::table('setting_analytics', function (Blueprint $table) {
            $table->renameColumn('head', 'value');
        });
    }
};
