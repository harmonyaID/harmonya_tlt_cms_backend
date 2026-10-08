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
        Schema::create('redirections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sourceUrl');
            $table->string('targetUrl');
            $table->unsignedSmallInteger('statusCode')->default(301);
            $table->boolean('isActive')->default(true);

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirections');
    }
};