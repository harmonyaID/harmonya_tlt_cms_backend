<?php

use App\Services\Property\PropertyAreaResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['property_addresses', 'property_variant_addresses'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'area')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('area')->nullable()->after('address')->index();
                });
            }

            // Existing rows: area found in the address, e.g. "Jungut Batu, Nusa Lembongan, ..." -> "Jungutbatu".
            $resolver = new PropertyAreaResolver();

            DB::table($table)
                ->whereNull('area')
                ->whereNotNull('address')
                ->orderBy('id')
                ->each(function ($row) use ($table, $resolver) {
                    if ($area = $resolver->resolve($row->address)) {
                        DB::table($table)->where('id', $row->id)->update(['area' => $area]);
                    }
                });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'area')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropIndex(['area']);
                    $t->dropColumn('area');
                });
            }
        }
    }
};
