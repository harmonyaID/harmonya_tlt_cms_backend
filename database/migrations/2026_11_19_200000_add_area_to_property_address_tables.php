<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Property Area master (like Property Type) + "areaId" on the property / variant
 * addresses, filled from the address text (e.g. "Jungut Batu, ..." -> Jungutbatu).
 */
return new class extends Migration
{
    use HasCustomMigration;

    private array $tables = ['property_addresses', 'property_variant_addresses'];

    /** Default areas => keyword in the address. */
    private array $areas = [
        'Jungutbatu' => '/jungut\s*batu/i',
        'Mushroom Bay' => '/mushroom/i',
        'Sandy Bay' => '/sandy\s*bay|dream\s*beach/i',
        'Tamarind Bay' => '/tamarind/i',
        'Nusa Ceningan' => '/ceningan/i',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('property_areas')) {
            Schema::create('property_areas', function (Blueprint $table) {
                $table->id();
                $table->string('name');

                $this->getDefaultTimestamps($table);
            });
        }

        $ids = [];
        foreach (array_keys($this->areas) as $name) {
            $ids[$name] = DB::table('property_areas')->where('name', $name)->value('id')
                ?? DB::table('property_areas')->insertGetId(['name' => $name, 'createdAt' => now(), 'updatedAt' => now()]);
        }

        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'areaId')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('areaId')->nullable()->after('address')->index();
                });
            }

            DB::table($table)
                ->whereNull('areaId')
                ->whereNotNull('address')
                ->orderBy('id')
                ->each(function ($row) use ($table, $ids) {
                    foreach ($this->areas as $name => $pattern) {
                        if (preg_match($pattern, $row->address)) {
                            DB::table($table)->where('id', $row->id)->update(['areaId' => $ids[$name]]);
                            break;
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'areaId')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropIndex(['areaId']);
                    $t->dropColumn('areaId');
                });
            }
        }

        Schema::dropIfExists('property_areas');
    }
};
