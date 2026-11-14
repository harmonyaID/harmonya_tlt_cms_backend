<?php

namespace App\Services\Property;

use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use App\Services\Constant\Storage\PathConstant;

class PropertyPhotoSyncService
{
    /**
     * Copy the photos of the property's first variant (lowest id) to the property itself.
     * Returns the number of photos copied; 0 when skipped.
     */
    public function syncFromFirstVariant(Property $property, bool $force = false): int
    {
        $hasPhotos = $property->photos()->exists();

        if ($hasPhotos && !$force) {
            return 0;
        }

        $first = $property->variants()->orderBy('id')->first();
        $photos = $first ? $first->photos()->get() : collect();

        if ($photos->isEmpty()) {
            return 0;
        }

        $dirPath = PathConstant::IMAGES_PROPERTY_PHOTO_STORAGE_PUBLIC_PATH();

        if ($force) {
            foreach ($property->photos as $old) {
                @unlink($dirPath . $old->path);
            }

            $property->photos()->delete();
        }

        $copied = 0;

        foreach ($photos as $photo) {
            $source = $dirPath . $photo->path;

            if (!is_file($source)) {
                continue;
            }

            $filename = 'property-' . $property->id . '-' . $photo->path;
            copy($source, $dirPath . $filename);

            PropertyPhoto::create([
                'propertyId' => $property->id,
                'path' => $filename,
                'caption' => $photo->caption,
                'order' => $photo->order,
            ]);

            $copied++;
        }

        return $copied;
    }
}
