<?php

namespace App\Algorithms\IslandGuide;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\IslandGuide\IslandGuideArea;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IslandGuideAreaAlgo
{
    public function __construct(protected IslandGuideArea|int|null $islandGuideArea = null)
    {
        if (is_int($this->islandGuideArea)) {
            $this->islandGuideArea = IslandGuideArea::find($this->islandGuideArea);
            if (!$this->islandGuideArea) errIslandGuideAreaGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->islandGuideArea = IslandGuideArea::create(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner']) + created_by()
                );
                if (!$this->islandGuideArea) errIslandGuideAreaSave();

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->islandGuideArea->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->islandGuideArea->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->islandGuideArea->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->islandGuideArea->save();
                }

                (new ContentSeoAlgo($this->islandGuideArea))->save($request);

                activity()->setCausedBy()->setReference($this->islandGuideArea)
                    ->setType(ActivityType::ISLAND_GUIDE_AREA)->setAction(ActivityAction::CREATE)
                    ->log("Enter new island-guide area: " . $this->islandGuideArea->name);
            });

            return success($this->islandGuideArea->load('type', 'seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->islandGuideArea->update(
                    $request->except(['featuredImage', 'banner', 'deleteFeaturedImage', 'deleteBanner'])
                );

                if ($request->boolean('deleteFeaturedImage')) {
                    $this->deleteImage($this->islandGuideArea->featuredImage);
                    $this->islandGuideArea->featuredImage = null;
                    $this->islandGuideArea->save();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->deleteImage($this->islandGuideArea->featuredImage);
                    $this->islandGuideArea->featuredImage = $this->uploadImage($request->file('featuredImage'), 'featured');
                    $this->islandGuideArea->save();
                }

                if ($request->boolean('deleteBanner')) {
                    $this->deleteImage($this->islandGuideArea->banner);
                    $this->islandGuideArea->banner = null;
                    $this->islandGuideArea->save();
                }

                if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                    $this->deleteImage($this->islandGuideArea->banner);
                    $this->islandGuideArea->banner = $this->uploadImage($request->file('banner'), 'banner');
                    $this->islandGuideArea->save();
                }

                (new ContentSeoAlgo($this->islandGuideArea))->save($request);

                activity()->setCausedBy()->setReference($this->islandGuideArea)
                    ->setType(ActivityType::ISLAND_GUIDE_AREA)->setAction(ActivityAction::UPDATE)
                    ->log("Update island-guide area: " . $this->islandGuideArea->name);
            });

            return success($this->islandGuideArea->load('type', 'seo'));
        } catch (\Error $error) { exception($error); }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                $this->deleteImage($this->islandGuideArea->featuredImage);
                $this->deleteImage($this->islandGuideArea->banner);

                if (!$this->islandGuideArea->delete()) errIslandGuideAreaDelete();

                activity()->setCausedBy()->setReference($this->islandGuideArea)
                    ->setType(ActivityType::ISLAND_GUIDE_AREA)->setAction(ActivityAction::DELETE)
                    ->log("Delete island-guide area: " . $this->islandGuideArea->name);
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
        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_AREA_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filename = filename($file, $this->islandGuideArea->name . '-' . $prefix);
        $file->move($dirPath, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_AREA_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}
