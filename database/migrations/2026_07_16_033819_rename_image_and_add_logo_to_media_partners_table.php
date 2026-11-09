<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_partners', function (Blueprint $table) {
            $table->renameColumn('image', 'featuredImage');
        });

        Schema::table('media_partners', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('featuredImage');
            $table->tinyInteger('typeId')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('media_partners', function (Blueprint $table) {
            $table->dropColumn('logo');
        });

        Schema::table('media_partners', function (Blueprint $table) {
            $table->renameColumn('featuredImage', 'image');
        });
    }
};
