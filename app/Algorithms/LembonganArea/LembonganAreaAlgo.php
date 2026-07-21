<?php

namespace App\Algorithms\LembonganArea;

use App\Models\LembonganArea\LembonganArea;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LembonganAreaAlgo
{
    /**
     * @param LembonganArea|int|null $lembonganArea
     */
    public function __construct(protected LembonganArea|int|null $lembonganArea = null)
    {
        if (is_int($this->lembonganArea)) {
            $this->lembonganArea = LembonganArea::find($this->lembonganArea);
            if (!$this->lembonganArea) {
                errLembonganAreaGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->lembonganArea = LembonganArea::create(
                    $request->except(['featuredImage', 'deleteFeaturedImage']) + created_by()
                );
                if (!$this->lembonganArea) {
                    errLembonganAreaSave();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->lembonganArea->featuredImage = $this->uploadImage($request);
                    $this->lembonganArea->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->lembonganArea)
                    ->setType(ActivityType::LEMBONGAN_AREA)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new Lembongan area: " . $this->lembonganArea->name);

            });

            return success($this->lembonganArea);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->lembonganArea->update($request->except(['featuredImage', 'deleteFeaturedImage']));

                if ($request->boolean('deleteFeaturedImage')) {
                    $this->deleteImage($this->lembonganArea->featuredImage);
                    $this->lembonganArea->featuredImage = null;
                    $this->lembonganArea->save();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->deleteImage($this->lembonganArea->featuredImage);
                    $this->lembonganArea->featuredImage = $this->uploadImage($request);
                    $this->lembonganArea->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->lembonganArea)
                    ->setType(ActivityType::LEMBONGAN_AREA)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update Lembongan area: " . $this->lembonganArea->name);

            });

            return success($this->lembonganArea);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $this->deleteImage($this->lembonganArea->featuredImage);

                if (!$this->lembonganArea->delete()) {
                    errLembonganAreaDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->lembonganArea)
                    ->setType(ActivityType::LEMBONGAN_AREA)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete Lembongan area: " . $this->lembonganArea->name);

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
        $image = $request->file('featuredImage');

        $dirPath = PathConstant::IMAGES_LEMBONGAN_AREA_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filename = filename($image, $this->lembonganArea->name);
        $image->move($dirPath, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $dirPath = PathConstant::IMAGES_LEMBONGAN_AREA_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}
