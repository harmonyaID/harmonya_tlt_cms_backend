<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guesty_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('credentialHash', 64)->index();
            $table->text('accessToken');
            $table->timestamp('expiresAt')->index();
            $table->timestamp('createdAt')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guesty_access_tokens');
    }
};
