<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * For databases that ran the first version of 2026_11_19_200000 (a text "area"
 * on the addresses): every text value becomes a Property Area, the addresses get
 * its "areaId", and the text column is dropped. Nothing to do on a new database.
 */
return new class extends Migration
{
    use HasCustomMigration;

    private array $tables = ['property_addresses', 'property_variant_addresses'];

    public function up(): void
    {
        if (!Schema::hasTable('property_areas')) {
            Schema::create('property_areas', function (Blueprint $table) {
                $table->id();
                $table->string('name');

                $this->getDefaultTimestamps($table);
            });
        }

        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'areaId')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('areaId')->nullable()->after('address')->index();
                });
            }

            if (!Schema::hasColumn($table, 'area')) {
                continue;
            }

            $names = DB::table($table)->whereNotNull('area')->where('area', '!=', '')->distinct()->pluck('area');

            foreach ($names as $name) {
                $id = DB::table('property_areas')->whereNull('deletedAt')->where('name', $name)->value('id')
                    ?? DB::table('property_areas')->insertGetId(['name' => $name, 'createdAt' => now(), 'updatedAt' => now()]);

                DB::table($table)->whereNull('areaId')->where('area', $name)->update(['areaId' => $id]);
            }

            Schema::table($table, function (Blueprint $t) use ($table) {
                if ($this->hasIndex($table, $table . '_area_index')) {
                    $t->dropIndex(['area']);
                }
                $t->dropColumn('area');
            });
        }
    }

    public function down(): void
    {
        // The text column is not brought back: areas stay in "areaId" (see 2026_11_19_200000).
    }

    private function hasIndex(string $table, string $index): bool
    {
        return collect(Schema::getIndexes($table))->contains(fn ($i) => $i['name'] === $index);
    }
};
