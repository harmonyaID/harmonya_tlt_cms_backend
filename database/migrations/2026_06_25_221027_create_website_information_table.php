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
        Schema::create('website_informations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('email');
            $table->char('phone')->nullable();
            $table->char('fax')->nullable();
            $table->char('whatsapp');
            $table->char('country');
            $table->char('postalCode');
            $table->string('address');
            $table->text('mapEmbed')->nullable();
            $table->json('socialMedia')->nullable();

            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_informations');
    }
};
