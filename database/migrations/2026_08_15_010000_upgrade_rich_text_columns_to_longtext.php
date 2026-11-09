<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $columns = [
        'property_descriptions' => ['summary' => true],
        'faqs' => ['answer' => false],
        'experiences' => ['description' => true],
        'experience_types' => ['description' => true],
        'experience_areas' => ['description' => true],
        'property_guest_infos' => [
            'houseManual' => true,
            'trashInstructions' => true,
            'parkingInstructions' => true,
            'cleaningInstructions' => true,
            'interactionWithGuests' => true,
        ],
        'property_availabilities' => ['checkInRestrictions' => true],
        'media_partners' => ['description' => true],
        'pages' => ['shortDescription' => false],
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column => $nullable) {
                $null = $nullable ? 'NULL' : 'NOT NULL';
                DB::statement("ALTER TABLE `$table` MODIFY `$column` LONGTEXT $null");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column => $nullable) {
                $null = $nullable ? 'NULL' : 'NOT NULL';
                DB::statement("ALTER TABLE `$table` MODIFY `$column` TEXT $null");
            }
        }
    }
};
