<?php

namespace App\Algorithms\TltTestimonial;

use App\Models\TltTestimonial\TltTestimonial;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TltTestimonialAlgo
{
    public function __construct(protected TltTestimonial|int|null $testimonial = null)
    {
        if (is_int($this->testimonial)) {
            $this->testimonial = TltTestimonial::find($this->testimonial);
            if (!$this->testimonial) {
                errTltTestimonialGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->testimonial = TltTestimonial::create($request->except(['photo', 'deletePhoto']) + created_by());
                if (!$this->testimonial) {
                    errTltTestimonialSave();
                }

                if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                    $this->testimonial->photo = $this->uploadPhoto($request);
                    $this->testimonial->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->testimonial)
                    ->setType(ActivityType::TLT_TESTIMONIAL)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new TLT testimonial: " . $this->testimonial->name);

            });

            return success($this->testimonial);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->testimonial->update($request->except(['photo', 'deletePhoto']));

                if ($request->boolean('deletePhoto')) {
                    $this->deletePhoto();
                    $this->testimonial->photo = null;
                    $this->testimonial->save();
                }

                if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                    $this->testimonial->photo = $this->uploadPhoto($request);
                    $this->testimonial->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->testimonial)
                    ->setType(ActivityType::TLT_TESTIMONIAL)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update TLT testimonial: " . $this->testimonial->name);

            });

            return success($this->testimonial);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $this->deletePhoto();

                if (!$this->testimonial->delete()) {
                    errTltTestimonialDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->testimonial)
                    ->setType(ActivityType::TLT_TESTIMONIAL)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete TLT testimonial: " . $this->testimonial->name);

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

    private function uploadPhoto(Request $request)
    {
        $photo = $request->file('photo');

        $dirPath = PathConstant::IMAGES_TLT_TESTIMONIAL_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $this->deletePhoto();

        $filename = filename($photo, $this->testimonial->name);
        $photo->move($dirPath, $filename);

        return $filename;
    }

    private function deletePhoto(): void
    {
        if (!$this->testimonial->photo) {
            return;
        }

        $dirPath = PathConstant::IMAGES_TLT_TESTIMONIAL_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $this->testimonial->photo)) {
            unlink($dirPath . $this->testimonial->photo);
        }
    }
}
