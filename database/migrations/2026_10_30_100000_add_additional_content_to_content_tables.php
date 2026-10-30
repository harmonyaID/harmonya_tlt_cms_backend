<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'boats',
        'experiences',
        'experience_types',
        'experience_areas',
        'island_guides',
        'island_guide_types',
        'island_guide_areas',
        'lembongan_areas',
        'media_partners',
        'offers',
        'blogs',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'additionalContent')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->longText('additionalContent')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'additionalContent')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('additionalContent');
                });
            }
        }
    }
};
