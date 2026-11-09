<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experience_areas', function (Blueprint $table) {
            $table->json('customInformations')->nullable()->after('description');
            $table->string('mapImage', 500)->nullable()->after('customInformations');
            $table->json('experiencePlayIds')->nullable()->after('banner');
            $table->json('experienceEatIds')->nullable()->after('experiencePlayIds');
            $table->json('propertyIds')->nullable()->after('experienceEatIds');
        });
    }

    public function down(): void
    {
        Schema::table('experience_areas', function (Blueprint $table) {
            $table->dropColumn([
                'customInformations',
                'mapImage',
                'banner',
                'experiencePlayIds',
                'experienceEatIds',
                'propertyIds',
            ]);
        });
    }
};
