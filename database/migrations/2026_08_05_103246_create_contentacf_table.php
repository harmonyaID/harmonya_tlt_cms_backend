<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasCustomMigration;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contentacf', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contentableId');
            $table->string('contentableType');

            $table->string('key')->nullable();
            $table->string('label')->nullable();

            $table->json('value')->nullable();

            $table->integer('order')->default(0);

            $this->getDefaultTimestamps($table);

            $table->index(['contentableId', 'contentableType']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contentacf');
    }
};
