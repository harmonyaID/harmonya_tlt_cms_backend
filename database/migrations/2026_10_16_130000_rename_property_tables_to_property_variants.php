<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** old table => new table */
    private array $tables = [
        'properties' => 'property_variants',
        'property_addresses' => 'property_variant_addresses',
        'property_guest_infos' => 'property_variant_guest_infos',
        'property_rooms' => 'property_variant_rooms',
        'property_availabilities' => 'property_variant_availabilities',
        'property_pricings' => 'property_variant_pricings',
        'property_descriptions' => 'property_variant_descriptions',
        'property_photos' => 'property_variant_photos',
        'property_amenity' => 'property_variant_amenity',
        'property_tag' => 'property_variant_tag',
        'property_feature' => 'property_variant_feature',
        'property_related' => 'property_variant_related',
        'property_reviews' => 'property_variant_reviews',
        'property_review_photos' => 'property_variant_review_photos',
    ];

    /** new table => [old column => new column] */
    private array $columns = [
        'property_variant_addresses' => ['propertyId' => 'propertyVariantId'],
        'property_variant_guest_infos' => ['propertyId' => 'propertyVariantId'],
        'property_variant_rooms' => ['propertyId' => 'propertyVariantId'],
        'property_variant_availabilities' => ['propertyId' => 'propertyVariantId'],
        'property_variant_pricings' => ['propertyId' => 'propertyVariantId'],
        'property_variant_descriptions' => ['propertyId' => 'propertyVariantId'],
        'property_variant_photos' => ['propertyId' => 'propertyVariantId'],
        'property_variant_amenity' => ['propertyId' => 'propertyVariantId'],
        'property_variant_tag' => ['propertyId' => 'propertyVariantId'],
        'property_variant_feature' => ['propertyId' => 'propertyVariantId'],
        'property_variant_related' => [
            'propertyId' => 'propertyVariantId',
            'relatedPropertyId' => 'relatedPropertyVariantId',
        ],
        'property_variant_reviews' => ['propertyId' => 'propertyVariantId'],
    ];

    private array $indexes = [
        'property_variants' => ['properties_guestyid_unique' => 'property_variants_guestyid_unique'],
        'property_variant_guest_infos' => ['property_guest_infos_propertyid_unique' => 'property_variant_guest_infos_propertyvariantid_unique'],
        'property_variant_availabilities' => ['property_availabilities_propertyid_unique' => 'property_variant_availabilities_propertyvariantid_unique'],
        'property_variant_pricings' => ['property_pricings_propertyid_unique' => 'property_variant_pricings_propertyvariantid_unique'],
    ];

    private array $classes = [
        'App\\Models\\Property\\Property' => 'App\\Models\\Property\\PropertyVariant',
        'App\\Models\\Property\\PropertyReview' => 'App\\Models\\Property\\PropertyVariantReview',
        'App\\Models\\Property\\PropertyPhoto' => 'App\\Models\\Property\\PropertyVariantPhoto',
    ];

    private array $activityTypes = [
        'property' => 'property_variant',
        'property_review' => 'property_variant_review',
    ];

    public function up(): void
    {
        foreach ($this->tables as $old => $new) {
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }

        foreach ($this->columns as $table => $renames) {
            foreach ($renames as $old => $new) {
                if (Schema::hasColumn($table, $old) && !Schema::hasColumn($table, $new)) {
                    Schema::table($table, fn (Blueprint $t) => $t->renameColumn($old, $new));
                }
            }
        }

        foreach ($this->indexes as $table => $renames) {
            foreach ($renames as $old => $new) {
                if (Schema::hasIndex($table, $old)) {
                    Schema::table($table, fn (Blueprint $t) => $t->renameIndex($old, $new));
                }
            }
        }

        $this->repointPolymorphicRows($this->classes);
        $this->renameActivityTypes($this->activityTypes);
    }

    public function down(): void
    {
        $this->renameActivityTypes(array_flip($this->activityTypes));
        $this->repointPolymorphicRows(array_flip($this->classes));

        foreach ($this->indexes as $table => $renames) {
            foreach ($renames as $old => $new) {
                if (Schema::hasIndex($table, $new)) {
                    Schema::table($table, fn (Blueprint $t) => $t->renameIndex($new, $old));
                }
            }
        }

        foreach ($this->columns as $table => $renames) {
            foreach ($renames as $old => $new) {
                if (Schema::hasColumn($table, $new) && !Schema::hasColumn($table, $old)) {
                    Schema::table($table, fn (Blueprint $t) => $t->renameColumn($new, $old));
                }
            }
        }

        foreach (array_reverse($this->tables, true) as $old => $new) {
            if (Schema::hasTable($new) && !Schema::hasTable($old)) {
                Schema::rename($new, $old);
            }
        }
    }

    private function repointPolymorphicRows(array $classes): void
    {
        foreach ($classes as $from => $to) {
            if (Schema::hasTable('contentseo')) {
                DB::table('contentseo')->where('contentableType', $from)->update(['contentableType' => $to]);
            }

            if (Schema::hasTable('contentacf')) {
                DB::table('contentacf')->where('contentableType', $from)->update(['contentableType' => $to]);
            }

            if (Schema::hasTable('activities')) {
                DB::table('activities')->where('referenceType', $from)->update(['referenceType' => $to]);
            }
        }
    }

    private function renameActivityTypes(array $types): void
    {
        if (!Schema::hasTable('activities')) {
            return;
        }

        foreach ($types as $from => $to) {
            DB::table('activities')->where('type', $from)->update(['type' => $to]);
        }
    }
};
