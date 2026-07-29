<?php

namespace App\Algorithms\Experience;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Experience\ExperienceType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceTypeAlgo
{
    public function __construct(protected ExperienceType|int|null $experienceType = null)
    {
        if (is_int($this->experienceType)) {
            $this->experienceType = ExperienceType::find($this->experienceType);
            if (!$this->experienceType) errExperienceTypeGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceType = ExperienceType::create(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner']) + created_by()
                );
                if (!$this->experienceType) errExperienceTypeSave();

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->experienceType->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->experienceType->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->experienceType->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->experienceType->save();
                }

                (new ContentSeoAlgo($this->experienceType))->save($request);

                activity()->setCausedBy()->setReference($this->experienceType)
                    ->setType(ActivityType::EXPERIENCE_TYPE)->setAction(ActivityAction::CREATE)
                    ->log("Enter new experience type: " . $this->experienceType->name);
            });

            return success($this->experienceType->load('seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceType->update(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner'])
                );

                if ($request->boolean('deleteFeaturedImage')) {
                    $this->deleteImage($this->experienceType->featuredImage);
                    $this->experienceType->featuredImage = null;
                    $this->experienceType->save();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->deleteImage($this->experienceType->featuredImage);
                    $this->experienceType->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->experienceType->save();
                }

                if ($request->boolean('deleteBanner')) {
                    $this->deleteImage($this->experienceType->banner);
                    $this->experienceType->banner = null;
                    $this->experienceType->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->deleteImage($this->experienceType->banner);
                    $this->experienceType->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->experienceType->save();
                }

                (new ContentSeoAlgo($this->experienceType))->save($request);

                activity()->setCausedBy()->setReference($this->experienceType)
                    ->setType(ActivityType::EXPERIENCE_TYPE)->setAction(ActivityAction::UPDATE)
                    ->log("Update experience type: " . $this->experienceType->name);
            });

            return success($this->experienceType->load('seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                $this->deleteImage($this->experienceType->featuredImage);
                $this->deleteImage($this->experienceType->banner);

                if (!$this->experienceType->delete()) errExperienceTypeDelete();

                activity()->setCausedBy()->setReference($this->experienceType)
                    ->setType(ActivityType::EXPERIENCE_TYPE)->setAction(ActivityAction::DELETE)
                    ->log("Delete experience type: " . $this->experienceType->name);
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
        $dirPath = PathConstant::IMAGES_EXPERIENCE_TYPE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filename = filename($file, $this->experienceType->name . '-' . $prefix);
        $file->move($dirPath, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $dirPath = PathConstant::IMAGES_EXPERIENCE_TYPE_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}
