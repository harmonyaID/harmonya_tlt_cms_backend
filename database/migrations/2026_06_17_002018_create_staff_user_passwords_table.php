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
        Schema::create('staff_user_passwords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId');
            $table->string('token');
            $table->dateTime('expiredDate');

            $this->getDefaultTimestamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_user_passwords');
    }
};
