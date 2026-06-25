<?php
namespace Database\Seeders\Language;

use App\Models\Language\Language;
use App\Models\Language\LanguageGroup;
use App\Models\Language\Translation;
use App\Services\Constant\BaseIDName;
use App\Services\Constant\Language\LanguageGroupDefaultPath;
use App\Services\Constant\Page\ContentPage;
use Illuminate\Database\Seeder;

class TranslatorSeeder extends Seeder
{
    private $languages;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->languages = Language::all();

        Translation::truncate();

        $this->createMainWebsiteTranslations(ContentPage::class, 'content_type');

    }


    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */
    private function createMainWebsiteTranslations($className, $firstKey)
    {
        $group = LanguageGroup::ofPath(LanguageGroupDefaultPath::MAIN_WEBSITE[$className])->first();

        foreach ($className::OPTION as $option) {

            $key = 'helper_main_website__' . $firstKey . '_' . strtolower($option['title']);
            if (Translation::where('key', $key)->count() > 0) {
                continue;
            }

            $translations = [];
            foreach ($this->languages as $language) {
                $translations[$language->code] = BaseIDName::customDisplay($option['content']);
            }

            Translation::updateOrCreate(['key' => $key], [
                'groupId' => optional($group)->id,
                'translations' => $translations
            ]);

        }
    }

}
