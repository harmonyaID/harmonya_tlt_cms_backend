<?php

namespace App\Algorithms\Property;

use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyPhotoAlgo
{
    public function __construct(protected Property $property)
    {
    }

    public function upload(Request $request)
    {
        try {

            $photo = DB::transaction(function () use ($request) {

                $dirPath = PathConstant::IMAGES_PROPERTY_PHOTO_STORAGE_PUBLIC_PATH();
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 0777, true);
                }

                $image = $request->file('photo');
                $filename = filename($image, $this->property->nickname);
                $image->move($dirPath, $filename);

                $photo = PropertyPhoto::create([
                    'propertyId' => $this->property->id,
                    'path' => $filename,
                    'caption' => $request->caption,
                    'order' => $request->order ?? ($this->property->photos()->max('order') + 1),
                ] + created_by());

                if (!$photo) {
                    errPropertyPhotoSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->property)
                    ->setType(ActivityType::PROPERTY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Add photo to property: " . $this->property->nickname);

                return $photo;

            });

            return success($photo);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete(PropertyPhoto $photo)
    {
        try {

            DB::transaction(function () use ($photo) {

                $dirPath = PathConstant::IMAGES_PROPERTY_PHOTO_STORAGE_PUBLIC_PATH();
                if ($photo->path && file_exists($dirPath . $photo->path)) {
                    unlink($dirPath . $photo->path);
                }

                if (!$photo->delete()) {
                    errPropertyPhotoDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->property)
                    ->setType(ActivityType::PROPERTY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Delete photo from property: " . $this->property->nickname);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}