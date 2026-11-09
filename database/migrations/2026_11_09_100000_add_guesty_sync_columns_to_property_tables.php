<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['properties', 'property_variants'];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->integer('guestySyncStatusId')->nullable();
                $table->timestamp('guestySyncedAt')->nullable();
                $table->text('guestySyncError')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn(['guestySyncStatusId', 'guestySyncedAt', 'guestySyncError']);
            });
        }
    }
};
