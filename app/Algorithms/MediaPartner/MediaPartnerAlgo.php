<?php

namespace App\Algorithms\MediaPartner;

use App\Models\MediaPartner\MediaPartner;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MediaPartnerAlgo
{
    /**
     * @param MediaPartner|int|null $mediaPartner
     */
    public function __construct(protected MediaPartner|int|null $mediaPartner = null)
    {
        if (is_int($this->mediaPartner)) {
            $this->mediaPartner = MediaPartner::find($this->mediaPartner);
            if (!$this->mediaPartner) {
                errMediaPartnerGet();
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

                $this->mediaPartner = MediaPartner::create($request->except(['featuredImage', 'logo']) + created_by());
                if (!$this->mediaPartner) {
                    errMediaPartnerSave();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->mediaPartner->featuredImage = $this->uploadFeaturedImage($request);
                    $this->mediaPartner->save();
                }

                if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                    $this->mediaPartner->logo = $this->uploadLogo($request);
                    $this->mediaPartner->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->mediaPartner)
                    ->setType(ActivityType::MEDIA_PARTNER)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new media partner. Description: " . $this->mediaPartner->description);

            });

            return success($this->mediaPartner);

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

                $this->mediaPartner->update($request->except(['featuredImage', 'logo']));

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->mediaPartner->featuredImage = $this->uploadFeaturedImage($request);
                    $this->mediaPartner->save();
                }

                if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                    $this->mediaPartner->logo = $this->uploadLogo($request);
                    $this->mediaPartner->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->mediaPartner)
                    ->setType(ActivityType::MEDIA_PARTNER)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update media partner. Description: " . $this->mediaPartner->description);

            });

            return success($this->mediaPartner);

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

                if (!$this->mediaPartner->delete()) {
                    errMediaPartnerDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->mediaPartner)
                    ->setType(ActivityType::MEDIA_PARTNER)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete media partner. Description: " . $this->mediaPartner->description);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function uploadFeaturedImage(Request $request)
    {
        $featuredImage = $request->file('featuredImage');

        $dirPath = PathConstant::IMAGES_MEDIA_PARTNER_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($this->mediaPartner->featuredImage && file_exists($dirPath . $this->mediaPartner->featuredImage)) {
            unlink($dirPath . $this->mediaPartner->featuredImage);
        }

        $filename = filename($featuredImage, $this->mediaPartner->description ?: 'media-partner');
        $featuredImage->move($dirPath, $filename);

        return $filename;
    }

    private function uploadLogo(Request $request)
    {
        $logo = $request->file('logo');

        $dirPath = PathConstant::IMAGES_MEDIA_PARTNER_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($this->mediaPartner->logo && file_exists($dirPath . $this->mediaPartner->logo)) {
            unlink($dirPath . $this->mediaPartner->logo);
        }

        $filename = filename($logo, ($this->mediaPartner->description ?: 'media-partner') . '-logo');
        $logo->move($dirPath, $filename);

        return $filename;
    }
}