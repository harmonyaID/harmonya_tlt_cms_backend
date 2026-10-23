<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_partners', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->date('date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('media_partners', function (Blueprint $table) {
            $table->dropColumn(['title', 'date']);
        });
    }
};
