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
        Schema::create('staff_user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId');
            $table->string('jti', 100)->unique();
            $table->string('deviceName', 150)->nullable();
            $table->string('browser', 80)->nullable();
            $table->string('platform', 80)->nullable();
            $table->string('ipAddress', 45)->nullable();
            $table->text('userAgent')->nullable();
            $table->dateTime('lastActiveAt')->nullable();
            $table->dateTime('expiresAt')->nullable();
            $table->dateTime('revokedAt')->nullable();

            $this->getDefaultTimestamps($table);

            $table->index(['userId', 'revokedAt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_user_sessions');
    }
};
