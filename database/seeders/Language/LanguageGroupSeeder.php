<?php

namespace Database\Seeders\Language;

use App\Models\Language\LanguageGroup;
use App\Services\Constant\Language\LanguageGroupDefaultPath;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LanguageGroup::truncate();
        
        $mainWebsites = LanguageGroupDefaultPath::MAIN_WEBSITE;
        $mainWebsites = collect($mainWebsites)->values();

        $collections = "";
        foreach ($mainWebsites as $key => $mainWebsite) {

            if ($key > 0) {
                $collections .= ",";
            }

            $collections .= "('$mainWebsite')";

        }

        DB::insert("INSERT INTO `language_groups` (`path`) VALUES $collections");
    }
}
