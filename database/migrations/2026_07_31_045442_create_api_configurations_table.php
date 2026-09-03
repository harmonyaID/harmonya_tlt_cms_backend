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
        Schema::create('api_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // e.g. "Guesty Open API"
            $table->string('key')->unique();   // e.g. "guesty" - used for programmatic lookup
            $table->string('module')->nullable(); // e.g. "property" - which CMS module owns this integration
            $table->text('credentials')->nullable(); // encrypted JSON: {"client_id":"...","client_secret":"...",...}
            $table->boolean('isActive')->default(false);
            $table->timestamp('lastTestedAt')->nullable();
            $table->boolean('lastTestSuccessful')->nullable();

            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_configurations');
    }
};
