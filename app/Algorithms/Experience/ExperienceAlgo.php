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
                $this->experience = Experience::create($request->except('thumbnail', 'catalogPdf', 'photos', 'deletePhotoIds') + created_by());
                if (!$this->experience) errExperienceSave();

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->experience->thumbnail = $this->uploadThumbnail($request);
                    $this->experience->save();
                }

                if ($request->hasFile('catalogPdf') && $request->file('catalogPdf')->isValid()) {
                    $this->experience->catalogPdf = $this->uploadPdf($request);
                    $this->experience->save();
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                activity()->setCausedBy()->setReference($this->experience)
                    ->setType(ActivityType::EXPERIENCE)->setAction(ActivityAction::CREATE)
                    ->log("Enter new experience: " . $this->experience->name);
            });

            return success($this->experience->load('type', 'category', 'photos'));
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experience->update($request->except('thumbnail', 'catalogPdf', 'photos', 'deletePhotoIds'));

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->experience->thumbnail = $this->uploadThumbnail($request);
                    $this->experience->save();
                }

                if ($request->hasFile('catalogPdf') && $request->file('catalogPdf')->isValid()) {
                    $this->experience->catalogPdf = $this->uploadPdf($request);
                    $this->experience->save();
                }

                if ($request->has('deletePhotoIds')) {
                    $this->deletePhotos($request->deletePhotoIds);
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                activity()->setCausedBy()->setReference($this->experience)
                    ->setType(ActivityType::EXPERIENCE)->setAction(ActivityAction::UPDATE)
                    ->log("Update experience: " . $this->experience->name);
            });

            return success($this->experience->load('type', 'category', 'photos'));
        } catch (\Error $error) { exception($error); }
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

                if ($this->experience->catalogPdf && file_exists($pdfPath . $this->experience->catalogPdf)) {
                    unlink($pdfPath . $this->experience->catalogPdf);
                }

                foreach ($this->experience->photos as $photo) {
                    if (file_exists($imgPath . $photo->photo)) unlink($imgPath . $photo->photo);
                    $photo->delete();
                }

                if (!$this->experience->delete()) errExperienceDelete();

                activity()->setCausedBy()->setReference($this->experience)
                    ->setType(ActivityType::EXPERIENCE)->setAction(ActivityAction::DELETE)
                    ->log("Delete experience: " . $this->experience->name);
            });

            return success();
        } catch (\Error $error) { exception($error); }
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function uploadThumbnail(Request $request)
    {
        $file = $request->file('thumbnail');
        $dirPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) mkdir($dirPath, 0777, true);

        if ($this->experience->thumbnail && file_exists($dirPath . $this->experience->thumbnail)) {
            unlink($dirPath . $this->experience->thumbnail);
        }

        $filename = filename($file, $this->experience->name);
        $file->move($dirPath, $filename);
        return $filename;
    }

    private function uploadPdf(Request $request)
    {
        $file = $request->file('catalogPdf');
        $dirPath = PathConstant::PDF_EXPERIENCE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) mkdir($dirPath, 0777, true);

        if ($this->experience->catalogPdf && file_exists($dirPath . $this->experience->catalogPdf)) {
            unlink($dirPath . $this->experience->catalogPdf);
        }

        $filename = filename($file, $this->experience->name);
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
            ExperiencePhoto::create(['experienceId' => $this->experience->id, 'photo' => $filename, 'order' => $lastOrder]);
        }
    }

    private function deletePhotos(array $photoIds)
    {
        $dirPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();
        $photos = ExperiencePhoto::where('experienceId', $this->experience->id)->whereIn('id', $photoIds)->get();

        foreach ($photos as $photo) {
            if (file_exists($dirPath . $photo->photo)) unlink($dirPath . $photo->photo);
            $photo->delete();
        }
    }
}