<?php

namespace App\Algorithms\Language;

use App\Models\Language\Language;
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
     * @param Language|int|null $language
     */
    public function __construct(protected Language|int|null $language = null)
    {
        if (is_int($this->language)) {
            $this->language = Language::find($this->language);
            if (!$this->language) {
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
    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                if ($this->languageAlreadyExists($request)) {
                    errLanguageAlreadyExists();
                }

                $this->language = Language::create($request->except('main', 'images'));
                if (!$this->language) {
                    errLanguageSave();
                }

                if ($request->has('images') && $request->images != '') {

                    if ($request->hasFile('images') && $request->file('images')->isValid()) {
                        $this->language->images = $this->uploadImage($this->language, $request);
                    } else {
                        $this->language->images = $request->images;
                    }

                    $this->language->save();

                }

                if ($request->main) {
                    $this->language->changeMain($request->main);
                }

            });

            return success($this->language);

        } catch (\Error $error) {
            exception($error);
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

            if ($this->changeCodeOrCountry($this->language, $request)) {

                if ($this->languageAlreadyExists($request)) {
                    errLanguageAlreadyExists();
                }

            }

            $this->language->update($request->except('main', 'images'));

            if ($request->has('images') && $request->images != '') {

                if ($request->hasFile('images') && $request->file('images')->isValid()) {
                    $this->language->images = $this->uploadImage($this->language, $request);
                } else {
                    $this->language->images = $request->images;
                }

                $this->language->save();

            }

            if ($this->language->main != $request->main) {
                $this->language->changeMain($request->main);
            }

            });

            return success($this->language);

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete()
    {
        try {

            DB::transaction(function () {
                if ($this->language->main) {
                    $this->language->changeMain(false);
                }

                if (!$this->language->delete()) {
                    errLanguageDelete();
                }

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function changeMain(Request $request)
    {
        try {
            DB::transaction(function ($request) {
                 $this->language->changeMain($request->main);
            });

            return success($this->language);
        } catch (\Error $error) {
            exception($error);
        }
    }
    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function languageAlreadyExists(Request $request)
    {
        return Language::langAlreadyExists($request->code, $request->country);
    }

    private function changeCodeOrCountry(Language $language, Request $request)
    {
        return ($request->code != $language->code) || ($request->country != $language->country);
    }

    private function uploadImage(Language $language, Request $request)
    {
        $images = $request->file('images');

        $dirPath = PathConstant::IMAGES_LANGUAGE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filename = filename($images, $language->country);
        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }

        $images->move($dirPath, $filename);

        return $filename;
    }
}
