<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->repoint('properties', fromVariantToParent: true);
    }

    public function down(): void
    {
        $this->repoint('property_variants', fromVariantToParent: false);
    }

    private function repoint(string $newTarget, bool $fromVariantToParent): void
    {
        $keep = [];
        $seen = [];

        foreach (DB::table('blog_property')->orderBy('id')->get() as $row) {
            $propertyId = $fromVariantToParent
                ? DB::table('property_variants')->where('id', $row->propertyId)->value('parentPropertyId')
                : DB::table('property_variants')->where('parentPropertyId', $row->propertyId)->orderBy('id')->value('id');

            $key = $row->blogId . ':' . $propertyId;
            if (!$propertyId || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $keep[] = ['blogId' => $row->blogId, 'propertyId' => $propertyId, 'order' => $row->order];
        }

        Schema::table('blog_property', function (Blueprint $table) {
            $table->dropForeign(['propertyId']);
        });

        DB::table('blog_property')->delete();

        if ($keep) {
            DB::table('blog_property')->insert($keep);
        }

        Schema::table('blog_property', function (Blueprint $table) use ($newTarget) {
            $table->foreign('propertyId')->references('id')->on($newTarget)->cascadeOnDelete();
        });
    }
};
