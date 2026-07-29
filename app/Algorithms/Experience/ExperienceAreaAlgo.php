<?php

namespace App\Algorithms\Experience;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Experience\ExperienceArea;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceAreaAlgo
{
    public function __construct(protected ExperienceArea|int|null $experienceArea = null)
    {
        if (is_int($this->experienceArea)) {
            $this->experienceArea = ExperienceArea::find($this->experienceArea);
            if (!$this->experienceArea) errExperienceAreaGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceArea = ExperienceArea::create(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner']) + created_by()
                );
                if (!$this->experienceArea) errExperienceAreaSave();

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->experienceArea->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->experienceArea->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->experienceArea->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->experienceArea->save();
                }

                (new ContentSeoAlgo($this->experienceArea))->save($request);

                activity()->setCausedBy()->setReference($this->experienceArea)
                    ->setType(ActivityType::EXPERIENCE_AREA)->setAction(ActivityAction::CREATE)
                    ->log("Enter new experience area: " . $this->experienceArea->name);
            });

            return success($this->experienceArea->load('type', 'seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceArea->update(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner'])
                );

                if ($request->boolean('deleteFeaturedImage')) {
                    $this->deleteImage($this->experienceArea->featuredImage);
                    $this->experienceArea->featuredImage = null;
                    $this->experienceArea->save();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->deleteImage($this->experienceArea->featuredImage);
                    $this->experienceArea->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->experienceArea->save();
                }

                if ($request->boolean('deleteBanner')) {
                    $this->deleteImage($this->experienceArea->banner);
                    $this->experienceArea->banner = null;
                    $this->experienceArea->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->deleteImage($this->experienceArea->banner);
                    $this->experienceArea->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->experienceArea->save();
                }

                (new ContentSeoAlgo($this->experienceArea))->save($request);

                activity()->setCausedBy()->setReference($this->experienceArea)
                    ->setType(ActivityType::EXPERIENCE_AREA)->setAction(ActivityAction::UPDATE)
                    ->log("Update experience area: " . $this->experienceArea->name);
            });

            return success($this->experienceArea->load('type', 'seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                $this->deleteImage($this->experienceArea->featuredImage);
                $this->deleteImage($this->experienceArea->banner);

                if (!$this->experienceArea->delete()) errExperienceAreaDelete();

                activity()->setCausedBy()->setReference($this->experienceArea)
                    ->setType(ActivityType::EXPERIENCE_AREA)->setAction(ActivityAction::DELETE)
                    ->log("Delete experience area: " . $this->experienceArea->name);
            });

            return success();
        } catch (\Error $error) { exception($error); }
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function uploadImage($file, string $prefix): string
    {
        $dirPath = PathConstant::IMAGES_EXPERIENCE_AREA_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filename = filename($file, $this->experienceArea->name . '-' . $prefix);
        $file->move($dirPath, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $dirPath = PathConstant::IMAGES_EXPERIENCE_AREA_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}
