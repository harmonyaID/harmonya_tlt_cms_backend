<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Columns holding content that's typically edited via a rich text / WYSIWYG
     * editor. MySQL's TEXT type caps out at 65,535 bytes (~65KB) - generous for
     * plain text, but WYSIWYG output (nested tags, inline styles, and especially
     * pasted/base64 images) can realistically exceed that on longer pages.
     * LONGTEXT raises the cap to 4GB, effectively removing the ceiling.
     *
     * value = original nullability, so we don't accidentally loosen/tighten
     * a NOT NULL column while widening its type.
     */
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

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column => $nullable) {
                $null = $nullable ? 'NULL' : 'NOT NULL';
                DB::statement("ALTER TABLE `$table` MODIFY `$column` LONGTEXT $null");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
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
