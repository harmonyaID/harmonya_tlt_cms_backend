
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'property_pricings',
        'property_variant_pricings',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            if (!Schema::hasColumn($tableName, 'monthlyDiscount')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('monthlyDiscount', 5, 2)
                    ->nullable()
                    ->default(0)
                    ->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            if (!Schema::hasColumn($tableName, 'monthlyDiscount')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('monthlyDiscount', 5, 2)
                    ->nullable(false)
                    ->default(0)
                    ->change();
            });
        }
    }
};
