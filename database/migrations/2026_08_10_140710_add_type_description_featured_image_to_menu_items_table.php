<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('typeId')->default(1)->after('menuUrl');
            $table->text('description')->nullable()->after('typeId');
            $table->string('featuredImage')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['typeId', 'description', 'featuredImage']);
        });
    }
};
