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
        Schema::table('boat_custom_informations', function (Blueprint $table) {
            $table->string('groupName')->nullable()->after('boatId');
        });
    }

    public function down(): void
    {
        Schema::table('boat_custom_informations', function (Blueprint $table) {
            $table->dropColumn('groupName');
        });
    }
};
