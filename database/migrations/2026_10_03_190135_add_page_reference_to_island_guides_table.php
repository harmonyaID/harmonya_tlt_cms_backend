<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('island_guides', function (Blueprint $table) {
            $table->boolean('isPage')->default(false)->after('showInquiry');
            $table->foreignId('pageReference')
                ->nullable()
                ->after('isPage')
                ->constrained('pages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('island_guides', function (Blueprint $table) {
            $table->dropForeign(['pageReference']);
            $table->dropColumn(['isPage', 'pageReference']);
        });
    }
};