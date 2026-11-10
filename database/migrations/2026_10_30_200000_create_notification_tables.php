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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->unique();
            $table->string('name', 150);
            $table->boolean('isActive')->default(true);
            $table->boolean('sendInApp')->default(true);
            $table->boolean('sendPush')->default(true);

            $this->getDefaultTimestamps($table);
        });

        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffUserId');
            $table->string('type', 60);
            $table->string('title', 250);
            $table->text('body')->nullable();
            $table->string('refType', 150)->nullable();
            $table->unsignedBigInteger('refId')->nullable();
            $table->json('data')->nullable();
            $table->dateTime('readAt')->nullable();

            $this->getDefaultTimestamps($table);

            $table->index(['staffUserId', 'readAt']);
        });

        Schema::create('staff_user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffUserId');
            $table->string('fcmToken', 500)->unique();
            $table->string('platform', 30)->default('web');
            $table->text('userAgent')->nullable();
            $table->dateTime('lastUsedAt')->nullable();

            $this->getDefaultTimestamps($table);

            $table->index('staffUserId');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_user_devices');
        Schema::dropIfExists('staff_notifications');
        Schema::dropIfExists('notification_settings');
    }
};
