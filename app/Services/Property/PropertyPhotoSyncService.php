<?php

namespace App\Services\Property;

use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\PropertyPhoto;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\DB;

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

    /**
     * Copy the addresses of the property's first variant (lowest id) to the property itself.
     * Returns the number of addresses copied; 0 when skipped.
     */
    public function syncAddressesFromFirstVariant(Property $property, bool $force = false): int
    {
        if ($property->addresses()->exists() && !$force) {
            return 0;
        }

        $first = $property->variants()->orderBy('id')->first();
        $addresses = $first ? $first->addresses()->get() : collect();

        if ($addresses->isEmpty()) {
            return 0;
        }

        if ($force) {
            $property->addresses()->delete();
        }

        foreach ($addresses as $address) {
            PropertyAddress::create([
                'propertyId' => $property->id,
                'typeId' => $address->typeId,
                'address' => $address->address,
                'buildingName' => $address->buildingName,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'zipCode' => $address->zipCode,
            ]);
        }

        return $addresses->count();
    }

    /**
     * Fill the property's numeric features (e.g. Bedroom, Bed, Bathroom) from its variants,
     * using the highest value of all variants ("up to X").
     *
     * Without $force a value is only raised, never lowered, so a higher value set by an
     * admin is kept. With $force the value is replaced by the variants' highest value.
     * Returns the number of features written.
     */
    public function syncFeaturesFromVariants(Property $property, bool $force = false): int
    {
        $variantIds = $property->variants()->pluck('id');

        if ($variantIds->isEmpty()) {
            return 0;
        }

        $highest = [];

        DB::table('property_variant_feature')
            ->whereIn('propertyVariantId', $variantIds)
            ->whereNotNull('value')
            ->get(['featureId', 'value'])
            ->each(function ($row) use (&$highest) {
                if (!is_numeric($row->value)) {
                    return;
                }

                $value = $row->value + 0;

                if (!isset($highest[$row->featureId]) || $value > $highest[$row->featureId]) {
                    $highest[$row->featureId] = $value;
                }
            });

        if (!$highest) {
            return 0;
        }

        $current = $property->features()->get()
            ->mapWithKeys(fn ($feature) => [$feature->id => $feature->pivot->value]);

        $syncData = [];

        foreach ($highest as $featureId => $value) {
            $old = $current[$featureId] ?? null;

            if (!$force && is_numeric($old) && $old + 0 >= $value) {
                continue;
            }

            $syncData[$featureId] = ['value' => (string) $value];
        }

        if ($syncData) {
            $property->features()->syncWithoutDetaching($syncData);
        }

        return count($syncData);
    }
}
