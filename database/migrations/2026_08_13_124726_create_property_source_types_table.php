<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasCustomMigration;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_source_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $this->getDefaultTimestamps($table);
        });

        // Seed in this exact order so the auto-increment IDs (1, 2) match the
        // values already used by properties.sourceTypeId from the old static
        // App\Services\Constant\Property\PropertySourceType (GUESTY_ID=1, BOOKEASY_ID=2).
        DB::table('property_source_types')->insert([
            ['id' => 1, 'name' => 'Guesty', 'createdAt' => now(), 'updatedAt' => now()],
            ['id' => 2, 'name' => 'Bookeasy', 'createdAt' => now(), 'updatedAt' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_source_types');
    }
};
