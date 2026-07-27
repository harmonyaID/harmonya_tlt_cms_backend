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
            $table->json('emails')->nullable()->after('title');
        });

        // migrate existing single email value into the new emails[] structure
        DB::table('website_informations')->whereNotNull('email')->where('email', '!=', '')->get()->each(function ($row) {
            DB::table('website_informations')->where('id', $row->id)->update([
                'emails' => json_encode([
                    ['title' => 'Email', 'email' => $row->email],
                ]),
            ]);
        });

        Schema::table('website_informations', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_informations', function (Blueprint $table) {
            $table->string('email')->nullable()->after('title');
        });

        DB::table('website_informations')->whereNotNull('emails')->get()->each(function ($row) {
            $emails = json_decode($row->emails, true);
            $first = $emails[0]['email'] ?? null;
            if ($first) {
                DB::table('website_informations')->where('id', $row->id)->update(['email' => $first]);
            }
        });

        Schema::table('website_informations', function (Blueprint $table) {
            $table->dropColumn('emails');
        });
    }
};
