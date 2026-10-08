<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experience_areas', function (Blueprint $table) {
            $table->json('experienceSection1Ids')
                ->nullable()
                ->after('experiencePlayIds');

            $table->json('experienceSection2Ids')
                ->nullable()
                ->after('experienceSection1Ids');

            $table->json('blogIds')
                ->nullable()
                ->after('propertyIds');
        });

        Schema::table('experience_areas', function (Blueprint $table) {
            $table->dropColumn([
                'experiencePlayIds',
                'experienceEatIds',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('experience_areas', function (Blueprint $table) {
            $table->json('experiencePlayIds')
                ->nullable()
                ->after('banner');

            $table->json('experienceEatIds')
                ->nullable()
                ->after('experiencePlayIds');
        });

        Schema::table('experience_areas', function (Blueprint $table) {
            $table->dropColumn([
                'experienceSection1Ids',
                'experienceSection2Ids',
                'blogIds',
            ]);
        });
    }
};