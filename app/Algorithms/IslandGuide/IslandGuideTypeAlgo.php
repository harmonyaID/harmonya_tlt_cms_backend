<?php

namespace App\Algorithms\IslandGuide;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\IslandGuide\IslandGuideType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IslandGuideTypeAlgo
{
    public function __construct(protected IslandGuideType|int|null $islandGuideType = null)
    {
        if (is_int($this->islandGuideType)) {
            $this->islandGuideType = IslandGuideType::find($this->islandGuideType);
            if (!$this->islandGuideType) errIslandGuideTypeGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->islandGuideType = IslandGuideType::create(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner']) + created_by()
                );
                if (!$this->islandGuideType) errIslandGuideTypeSave();

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->islandGuideType->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->islandGuideType->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->islandGuideType->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->islandGuideType->save();
                }

                (new ContentSeoAlgo($this->islandGuideType))->save($request);

                activity()->setCausedBy()->setReference($this->islandGuideType)
                    ->setType(ActivityType::ISLAND_GUIDE_TYPE)->setAction(ActivityAction::CREATE)
                    ->log("Enter new island-guide type: " . $this->islandGuideType->name);
            });

            return success($this->islandGuideType->load('seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->islandGuideType->update(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner'])
                );

                if ($request->boolean('deleteFeaturedImage')) {
                    $this->deleteImage($this->islandGuideType->featuredImage);
                    $this->islandGuideType->featuredImage = null;
                    $this->islandGuideType->save();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->deleteImage($this->islandGuideType->featuredImage);
                    $this->islandGuideType->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->islandGuideType->save();
                }

                if ($request->boolean('deleteBanner')) {
                    $this->deleteImage($this->islandGuideType->banner);
                    $this->islandGuideType->banner = null;
                    $this->islandGuideType->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->deleteImage($this->islandGuideType->banner);
                    $this->islandGuideType->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->islandGuideType->save();
                }

                (new ContentSeoAlgo($this->islandGuideType))->save($request);

                activity()->setCausedBy()->setReference($this->islandGuideType)
                    ->setType(ActivityType::ISLAND_GUIDE_TYPE)->setAction(ActivityAction::UPDATE)
                    ->log("Update island-guide type: " . $this->islandGuideType->name);
            });

            return success($this->islandGuideType->load('seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                $this->deleteImage($this->islandGuideType->featuredImage);
                $this->deleteImage($this->islandGuideType->banner);

                if (!$this->islandGuideType->delete()) errIslandGuideTypeDelete();

                activity()->setCausedBy()->setReference($this->islandGuideType)
                    ->setType(ActivityType::ISLAND_GUIDE_TYPE)->setAction(ActivityAction::DELETE)
                    ->log("Delete island-guide type: " . $this->islandGuideType->name);
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
        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_TYPE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filename = filename($file, $this->islandGuideType->name . '-' . $prefix);
        $file->move($dirPath, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_TYPE_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}
