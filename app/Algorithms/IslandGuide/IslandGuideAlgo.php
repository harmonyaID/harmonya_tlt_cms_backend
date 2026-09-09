<?php

namespace App\Algorithms\IslandGuide;

use App\Algorithms\Acf\ContentAcfAlgo;
use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\IslandGuide\IslandGuide;
use App\Models\IslandGuide\IslandGuidePhoto;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IslandGuideAlgo
{
    public function __construct(protected IslandGuide|int|null $islandGuide = null)
    {
        if (is_int($this->islandGuide)) {
            $this->islandGuide = IslandGuide::find($this->islandGuide);
            if (!$this->islandGuide) errIslandGuideGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->islandGuide = IslandGuide::create(
                    $request->except(
                        'thumbnail',
                        'mapImage',
                        'photos',
                        'deletePhotoIds',
                        'catalogs',
                        'deleteCatalogIds',
                        'seo',
                        'acf'
                    )
                );
                if (!$this->islandGuide) errIslandGuideSave();

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->islandGuide->thumbnail = $this->uploadImage($request->file('thumbnail'), 'thumbnail');
                    $this->islandGuide->save();
                }

                if ($request->hasFile('mapImage') && $request->file('mapImage')->isValid()) {
                    $this->islandGuide->mapImage = $this->uploadImage($request->file('mapImage'), 'map');
                    $this->islandGuide->save();
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                if ($request->has('catalogs')) {
                    $this->islandGuide->catalogs = $this->processCatalogs($request);
                    $this->islandGuide->save();
                }

                (new ContentSeoAlgo($this->islandGuide))->save($request);
                (new ContentAcfAlgo($this->islandGuide))->save($request);

                activity()->setCausedBy()->setReference($this->islandGuide)
                    ->setType(ActivityType::ISLAND_GUIDE)->setAction(ActivityAction::CREATE)
                    ->log("Enter new island-guide: " . $this->islandGuide->name);
            });

            return success($this->islandGuide->load('type', 'area', 'photos', 'seo', 'acf'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->islandGuide->update(
                    $request->except(
                        'thumbnail',
                        'mapImage',
                        'photos',
                        'deletePhotoIds',
                        'catalogs',
                        'deleteCatalogIds',
                        'seo',
                        'acf'
                    )
                );

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->islandGuide->thumbnail = $this->uploadImage($request->file('thumbnail'), 'thumbnail', $this->islandGuide->thumbnail);
                    $this->islandGuide->save();
                }

                if ($request->hasFile('mapImage') && $request->file('mapImage')->isValid()) {
                    $this->islandGuide->mapImage = $this->uploadImage($request->file('mapImage'), 'map', $this->islandGuide->mapImage);
                    $this->islandGuide->save();
                }

                if ($request->has('deletePhotoIds')) {
                    $this->deletePhotos($request->deletePhotoIds);
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }


                if ($request->has('catalogs') || $request->has('deleteCatalogIds')) {
                    $this->islandGuide->catalogs = $this->processCatalogs($request);
                    $this->islandGuide->save();
                }

                (new ContentSeoAlgo($this->islandGuide))->save($request);
                (new ContentAcfAlgo($this->islandGuide))->save($request);

                activity()->setCausedBy()->setReference($this->islandGuide)
                    ->setType(ActivityType::ISLAND_GUIDE)->setAction(ActivityAction::UPDATE)
                    ->log("Update island-guide: " . $this->islandGuide->name);
            });

            return success($this->islandGuide->load('type', 'area', 'photos', 'seo', 'acf'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                $this->islandGuide->acf()->delete();

                $imgPath = PathConstant::IMAGES_ISLAND_GUIDE_STORAGE_PUBLIC_PATH();
                $pdfPath = PathConstant::PDF_ISLAND_GUIDE_STORAGE_PUBLIC_PATH();

                if ($this->islandGuide->thumbnail && file_exists($imgPath . $this->islandGuide->thumbnail)) {
                    unlink($imgPath . $this->islandGuide->thumbnail);
                }

                if ($this->islandGuide->mapImage && file_exists($imgPath . $this->islandGuide->mapImage)) {
                    unlink($imgPath . $this->islandGuide->mapImage);
                }

                foreach ($this->islandGuide->photos as $photo) {
                    if (file_exists($imgPath . $photo->photo)) unlink($imgPath . $photo->photo);
                    $photo->delete();
                }

                if ($this->islandGuide->catalogs) {
                    foreach ($this->islandGuide->catalogs as $catalog) {
                        if (!empty($catalog['file']) && file_exists($pdfPath . $catalog['file'])) {
                            unlink($pdfPath . $catalog['file']);
                        }
                    }
                }

                if (!$this->islandGuide->delete()) errIslandGuideDelete();

                activity()->setCausedBy()->setReference($this->islandGuide)
                    ->setType(ActivityType::ISLAND_GUIDE)->setAction(ActivityAction::DELETE)
                    ->log("Delete island-guide: " . $this->islandGuide->name);
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

    private function uploadImage($file, string $prefix, ?string $oldFile = null)
    {
        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) mkdir($dirPath, 0777, true);

        if ($oldFile && file_exists($dirPath . $oldFile)) {
            unlink($dirPath . $oldFile);
        }

        $filename = filename($file, $this->islandGuide->name . '-' . $prefix);
        $file->move($dirPath, $filename);
        return $filename;
    }

    private function uploadPhotos(Request $request)
    {
        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) mkdir($dirPath, 0777, true);

        $lastOrder = IslandGuidePhoto::where('islandGuideId', $this->islandGuide->id)->max('order') ?? 0;

        foreach ($request->file('photos') as $photo) {
            if (!$photo->isValid()) continue;
            $lastOrder++;
            $filename = filename($photo, $this->islandGuide->name);
            $photo->move($dirPath, $filename);
            IslandGuidePhoto::create([
                'islandGuideId' => $this->islandGuide->id,
                'photo' => $filename,
                'order' => $lastOrder,
            ]);
        }
    }

    private function deletePhotos(array $photoIds)
    {
        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_STORAGE_PUBLIC_PATH();
        $photos = IslandGuidePhoto::where('islandGuideId', $this->islandGuide->id)
            ->whereIn('id', $photoIds)->get();

        foreach ($photos as $photo) {
            if (file_exists($dirPath . $photo->photo)) unlink($dirPath . $photo->photo);
            $photo->delete();
        }
    }

    private function processCatalogs(Request $request): array
    {
        $pdfPath = PathConstant::PDF_ISLAND_GUIDE_STORAGE_PUBLIC_PATH();

        if (!file_exists($pdfPath)) {
            mkdir($pdfPath, 0777, true);
        }

        $catalogs = $this->islandGuide->catalogs ?? [];

        if ($request->filled('deleteCatalogIds')) {

            foreach ($catalogs as $key => $catalog) {

                if (!in_array($catalog['id'], $request->deleteCatalogIds)) {
                    continue;
                }

                if (!empty($catalog['file']) && file_exists($pdfPath . $catalog['file'])) {
                    unlink($pdfPath . $catalog['file']);
                }

                unset($catalogs[$key]);
            }

            $catalogs = array_values($catalogs);
        }

        /*
     |--------------------------------------------------------------------------
     | Next ID
     |--------------------------------------------------------------------------
     */
        $ids = array_column($catalogs, 'id');
        $nextId = empty($ids) ? 1 : max($ids) + 1;

        /*
     |--------------------------------------------------------------------------
     | Update / Create
     |--------------------------------------------------------------------------
     */
        $requestCatalogs = $request->input('catalogs', []);
        $requestFiles = $request->file('catalogs', []);

        foreach ($requestCatalogs as $index => $item) {

            $catalogId = $item['id'] ?? null;
            $name = $item['name'];

            $file = $requestFiles[$index]['file'] ?? null;

            if ($catalogId) {

                foreach ($catalogs as &$catalog) {

                    if ($catalog['id'] != $catalogId) {
                        continue;
                    }

                    $catalog['name'] = $name;

                    if ($file && $file->isValid()) {

                        if (!empty($catalog['file']) && file_exists($pdfPath . $catalog['file'])) {
                            unlink($pdfPath . $catalog['file']);
                        }

                        $filename = filename($file, $this->islandGuide->name . '-' . $name);
                        $file->move($pdfPath, $filename);

                        $catalog['file'] = $filename;
                    }

                    break;
                }

                unset($catalog);

                continue;
            }

            if ($file && $file->isValid()) {

                $filename = filename($file, $this->islandGuide->name . '-' . $name);
                $file->move($pdfPath, $filename);

                $catalogs[] = [
                    'id'   => $nextId++,
                    'name' => $name,
                    'file' => $filename,
                ];
            }
        }

        return array_values($catalogs);
    }
}
