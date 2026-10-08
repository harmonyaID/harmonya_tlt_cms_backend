<?php

use App\Services\Constant\Setting\Provider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_notification_credentials', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('providerId');

            $table->string('name');

            $table->json('credentials');

            $table->boolean('isActive')->default(true);

            $table->unsignedBigInteger('createdBy')->nullable();
            $table->unsignedBigInteger('updatedBy')->nullable();

            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();

            $table->softDeletes('deletedAt');

            $table->index(['providerId', 'isActive']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_notification_credentials');
    }
};