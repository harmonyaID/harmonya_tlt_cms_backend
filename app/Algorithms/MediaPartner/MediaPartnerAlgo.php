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

                $this->mediaPartner = MediaPartner::create($request->except('image') + created_by());
                if (!$this->mediaPartner) {
                    errMediaPartnerSave();
                }

                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $this->mediaPartner->image = $this->uploadImage($request);
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

                $this->mediaPartner->update($request->except('image'));

                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $this->mediaPartner->image = $this->uploadImage($request);
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

    private function uploadImage(Request $request)
    {
        $image = $request->file('image');

        $dirPath = PathConstant::IMAGES_MEDIA_PARTNER_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($this->mediaPartner->image && file_exists($dirPath . $this->mediaPartner->image)) {
            unlink($dirPath . $this->mediaPartner->image);
        }

        $filename = filename($image, $this->mediaPartner->description ?: 'media-partner');
        $image->move($dirPath, $filename);

        return $filename;
    }
}