<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contentableId');
            $table->string('contentableType');
            $table->string('ipAddress', 45); // 45 chars fits IPv6
            $table->string('userAgent')->nullable();
            $table->timestamp('createdAt')->nullable();

            $table->unique(['contentableType', 'contentableId', 'ipAddress'], 'content_visitors_unique_ip');

            $table->index(['contentableType', 'contentableId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_visitors');
    }
};
