<?php

namespace App\Algorithms\Experience;

use App\Models\Experience\Experience;
use App\Models\Experience\ExperiencePhoto;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceAlgo
{
    public function __construct(protected Experience|int|null $experience = null)
    {
        if (is_int($this->experience)) {
            $this->experience = Experience::find($this->experience);
            if (!$this->experience) errExperienceGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->experience = Experience::create(
                    $request->except(
                        'thumbnail',
                        'mapImage',
                        'photos',
                        'deletePhotoIds',
                        'catalogs',
                        'deleteCatalogIds'
                    )
                );
                if (!$this->experience) errExperienceSave();

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->experience->thumbnail = $this->uploadImage($request->file('thumbnail'), 'thumbnail');
                    $this->experience->save();
                }

                if ($request->hasFile('mapImage') && $request->file('mapImage')->isValid()) {
                    $this->experience->mapImage = $this->uploadImage($request->file('mapImage'), 'map');
                    $this->experience->save();
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                if ($request->has('catalogs')) {
                    $this->experience->catalogs = $this->processCatalogs($request);
                    $this->experience->save();
                }

                activity()->setCausedBy()->setReference($this->experience)
                    ->setType(ActivityType::EXPERIENCE)->setAction(ActivityAction::CREATE)
                    ->log("Enter new experience: " . $this->experience->name);
            });

            return success($this->experience->load('type', 'category', 'photos'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->experience->update(
                    $request->except(
                        'thumbnail',
                        'mapImage',
                        'photos',
                        'deletePhotoIds',
                        'catalogs',
                        'deleteCatalogIds'
                    )
                );

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->experience->thumbnail = $this->uploadImage($request->file('thumbnail'), 'thumbnail', $this->experience->thumbnail);
                    $this->experience->save();
                }

                if ($request->hasFile('mapImage') && $request->file('mapImage')->isValid()) {
                    $this->experience->mapImage = $this->uploadImage($request->file('mapImage'), 'map', $this->experience->mapImage);
                    $this->experience->save();
                }

                if ($request->has('deletePhotoIds')) {
                    $this->deletePhotos($request->deletePhotoIds);
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }


                if ($request->has('catalogs') || $request->has('deleteCatalogIds')) {
                    $this->experience->catalogs = $this->processCatalogs($request);
                    $this->experience->save();
                }

                activity()->setCausedBy()->setReference($this->experience)
                    ->setType(ActivityType::EXPERIENCE)->setAction(ActivityAction::UPDATE)
                    ->log("Update experience: " . $this->experience->name);
            });

            return success($this->experience->load('type', 'category', 'photos'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                $imgPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();
                $pdfPath = PathConstant::PDF_EXPERIENCE_STORAGE_PUBLIC_PATH();

                if ($this->experience->thumbnail && file_exists($imgPath . $this->experience->thumbnail)) {
                    unlink($imgPath . $this->experience->thumbnail);
                }

                if ($this->experience->mapImage && file_exists($imgPath . $this->experience->mapImage)) {
                    unlink($imgPath . $this->experience->mapImage);
                }

                foreach ($this->experience->photos as $photo) {
                    if (file_exists($imgPath . $photo->photo)) unlink($imgPath . $photo->photo);
                    $photo->delete();
                }

                if ($this->experience->catalogs) {
                    foreach ($this->experience->catalogs as $catalog) {
                        if (!empty($catalog['file']) && file_exists($pdfPath . $catalog['file'])) {
                            unlink($pdfPath . $catalog['file']);
                        }
                    }
                }

                if (!$this->experience->delete()) errExperienceDelete();

                activity()->setCausedBy()->setReference($this->experience)
                    ->setType(ActivityType::EXPERIENCE)->setAction(ActivityAction::DELETE)
                    ->log("Delete experience: " . $this->experience->name);
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
        $dirPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) mkdir($dirPath, 0777, true);

        if ($oldFile && file_exists($dirPath . $oldFile)) {
            unlink($dirPath . $oldFile);
        }

        $filename = filename($file, $this->experience->name . '-' . $prefix);
        $file->move($dirPath, $filename);
        return $filename;
    }

    private function uploadPhotos(Request $request)
    {
        $dirPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) mkdir($dirPath, 0777, true);

        $lastOrder = ExperiencePhoto::where('experienceId', $this->experience->id)->max('order') ?? 0;

        foreach ($request->file('photos') as $photo) {
            if (!$photo->isValid()) continue;
            $lastOrder++;
            $filename = filename($photo, $this->experience->name);
            $photo->move($dirPath, $filename);
            ExperiencePhoto::create([
                'experienceId' => $this->experience->id,
                'photo' => $filename,
                'order' => $lastOrder,
            ]);
        }
    }

    private function deletePhotos(array $photoIds)
    {
        $dirPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();
        $photos = ExperiencePhoto::where('experienceId', $this->experience->id)
            ->whereIn('id', $photoIds)->get();

        foreach ($photos as $photo) {
            if (file_exists($dirPath . $photo->photo)) unlink($dirPath . $photo->photo);
            $photo->delete();
        }
    }

    private function processCatalogs(Request $request): array
    {
        $pdfPath = PathConstant::PDF_EXPERIENCE_STORAGE_PUBLIC_PATH();

        if (!file_exists($pdfPath)) {
            mkdir($pdfPath, 0777, true);
        }

        $catalogs = $this->experience->catalogs ?? [];

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

                        $filename = filename($file, $this->experience->name . '-' . $name);
                        $file->move($pdfPath, $filename);

                        $catalog['file'] = $filename;
                    }

                    break;
                }

                unset($catalog);

                continue;
            }

            if ($file && $file->isValid()) {

                $filename = filename($file, $this->experience->name . '-' . $name);
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
