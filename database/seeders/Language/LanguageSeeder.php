<?php

namespace Database\Seeders\Language;

use App\Models\Language\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            [
                'code' => 'en',
                'country' => 'English',
                'main' => true
            ],
            [
                'code' => 'id',
                'country' => 'Indonesian',
                'main' => false
            ],
        );

        Language::insert(collect($data)->filter(function ($language) {
            return Language::where('code', $language['code'])->count() == 0;
        })->toArray());
    }
}
