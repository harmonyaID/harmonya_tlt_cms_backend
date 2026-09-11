<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('boats', fn(Blueprint $table) => $table->string('promoLabel')->nullable()->after('promoPhotos')); } public function down(): void { Schema::table('boats', fn(Blueprint $table) => $table->dropColumn('promoLabel')); } };
