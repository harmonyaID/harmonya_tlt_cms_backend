<?php

namespace App\Algorithms\Boat;

use App\Models\Boat\Boat;
use App\Models\Boat\BoatPhoto;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatAlgo
{
    public function __construct(protected Boat|int|null $boat = null)
    {
        if (is_int($this->boat)) {
            $this->boat = Boat::find($this->boat);
            if (!$this->boat) {
                errBoatGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boat = Boat::create($request->except('photos', 'deletePhotoIds') + created_by());
                if (!$this->boat) {
                    errBoatSave();
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                activity()->setCausedBy()
                    ->setReference($this->boat)
                    ->setType(ActivityType::BOAT)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new boat: " . $this->boat->name);

            });

            return success($this->boat->load('photos', 'types'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boat->update($request->except('photos', 'deletePhotoIds'));

                if ($request->has('deletePhotoIds')) {
                    $this->deletePhotos($request->deletePhotoIds);
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                activity()->setCausedBy()
                    ->setReference($this->boat)
                    ->setType(ActivityType::BOAT)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update boat: " . $this->boat->name);

            });

            return success($this->boat->load('photos', 'types'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $dirPath = PathConstant::IMAGES_BOAT_STORAGE_PUBLIC_PATH();
                foreach ($this->boat->photos as $photo) {
                    if (file_exists($dirPath . $photo->photo)) {
                        unlink($dirPath . $photo->photo);
                    }
                    $photo->delete();
                }

                if (!$this->boat->delete()) {
                    errBoatDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->boat)
                    ->setType(ActivityType::BOAT)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete boat: " . $this->boat->name);

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

    private function uploadPhotos(Request $request)
    {
        $dirPath = PathConstant::IMAGES_BOAT_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $lastOrder = BoatPhoto::where('boatId', $this->boat->id)->max('order') ?? 0;

        foreach ($request->file('photos') as $photo) {
            if (!$photo->isValid()) {
                continue;
            }

            $lastOrder++;
            $filename = filename($photo, $this->boat->name);
            $photo->move($dirPath, $filename);

            BoatPhoto::create([
                'boatId' => $this->boat->id,
                'photo' => $filename,
                'order' => $lastOrder,
            ]);
        }
    }

    private function deletePhotos(array $photoIds)
    {
        $dirPath = PathConstant::IMAGES_BOAT_STORAGE_PUBLIC_PATH();

        $photos = BoatPhoto::where('boatId', $this->boat->id)
            ->whereIn('id', $photoIds)
            ->get();

        foreach ($photos as $photo) {
            if (file_exists($dirPath . $photo->photo)) {
                unlink($dirPath . $photo->photo);
            }
            $photo->delete();
        }
    }
}