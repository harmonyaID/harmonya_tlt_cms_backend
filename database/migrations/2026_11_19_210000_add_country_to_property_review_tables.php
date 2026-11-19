<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['property_reviews', 'property_variant_reviews'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'country')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('country')->nullable()->after('name');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'country')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('country');
                });
            }
        }
    }
};
