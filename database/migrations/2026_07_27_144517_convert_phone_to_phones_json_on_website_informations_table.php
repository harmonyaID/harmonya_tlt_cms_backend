<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('website_informations', function (Blueprint $table) {
            $table->json('phones')->nullable()->after('emails');
        });

        // migrate existing single phone value into the new phones[] structure
        DB::table('website_informations')->whereNotNull('phone')->where('phone', '!=', '')->get()->each(function ($row) {
            DB::table('website_informations')->where('id', $row->id)->update([
                'phones' => json_encode([
                    ['title' => 'Phone', 'phone' => $row->phone],
                ]),
            ]);
        });

        Schema::table('website_informations', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_informations', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('emails');
        });

        DB::table('website_informations')->whereNotNull('phones')->get()->each(function ($row) {
            $phones = json_decode($row->phones, true);
            $first = $phones[0]['phone'] ?? null;
            if ($first) {
                DB::table('website_informations')->where('id', $row->id)->update(['phone' => $first]);
            }
        });

        Schema::table('website_informations', function (Blueprint $table) {
            $table->dropColumn('phones');
        });
    }
};
