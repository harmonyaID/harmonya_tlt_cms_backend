<?php

namespace App\Algorithms\Language;

use App\Models\Language\Language;
use App\Models\Language\LanguageGroup;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class LanguageAlgo
{
    /**
     * @var string
     */
    protected string $newPassword = "";

    /**
     * @param LanguageGroup|int|null $languageGroup
     */
    public function __construct(protected LanguageGroup|int|null $languageGroup = null)
    {
        if (is_int($this->languageGroup)) {
            $this->languageGroup = LanguageGroup::find($this->languageGroup);
            if (!$this->languageGroup) {
                errLanguageGet();
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

            $langPaths = [];
            foreach (Language::all() as $language) {

                $path = PathConstant::LANG_STORAGE_PUBLIC_PATH($language->code) . $request->path . '.json';
                if (file_exists($path)) {
                    errLanguageGroupIsExists("Group in #$language->country ($language->code) already exists. Please delete first");
                }

                $langPaths[] = $path;

            }

            $this->languageGroup = LanguageGroup::firstOrCreate($request->all());
            if (!$this->languageGroup) {
                errLanguageGroupSave();
            }

            foreach ($langPaths as $langPath) {

                $path = str_replace("/" . basename($langPath), "", $langPath);
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                file_put_contents($langPath, json_encode((object)[]));

            }
            });

            return success($this->languageGroup);

        } catch (\Error $error) {
            exception($error);
        }
    }
}
