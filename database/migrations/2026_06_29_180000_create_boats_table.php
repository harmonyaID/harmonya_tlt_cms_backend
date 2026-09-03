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
        Schema::create('boats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boatComponentTypeId');
            $table->longText('description')->nullable();
            $table->json('promoPhotos')->nullable();
            $table->string('priceFile')->nullable();
            $table->boolean('isActive')->default(true);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boats');
    }
};