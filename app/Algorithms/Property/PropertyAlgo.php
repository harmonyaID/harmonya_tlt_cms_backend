<?php

namespace App\Algorithms\Property;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\PropertyAvailability;
use App\Models\Property\PropertyDescription;
use App\Models\Property\PropertyGuestInfo;
use App\Models\Property\PropertyPricing;
use App\Models\Property\PropertyRoom;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyAlgo
{
    protected array $nestedKeys = [
        'addresses', 'guestInfo', 'rooms', 'availability', 'pricing',
        'descriptions', 'amenityIds', 'tagIds', 'features', 'seo',
    ];

    public function __construct(protected Property|int|null $property = null)
    {
        if (is_int($this->property)) {
            $this->property = Property::find($this->property);
            if (!$this->property) {
                errPropertyGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $data = $request->except($this->nestedKeys);

                $this->property = Property::create($data + created_by());
                if (!$this->property) {
                    errPropertySave();
                }

                $this->syncNested($request);

                (new ContentSeoAlgo($this->property))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->property)
                    ->setType(ActivityType::PROPERTY)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property: " . $this->property->nickname);

            });

            return success($this->property->load($this->relations()));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $data = $request->except($this->nestedKeys);
                $this->property->update($data);

                $this->syncNested($request);

                (new ContentSeoAlgo($this->property))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->property)
                    ->setType(ActivityType::PROPERTY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property: " . $this->property->nickname);

            });

            return success($this->property->load($this->relations()));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $dirPath = PathConstant::IMAGES_PROPERTY_PHOTO_STORAGE_PUBLIC_PATH();
                foreach ($this->property->photos as $photo) {
                    if ($photo->path && file_exists($dirPath . $photo->path)) {
                        unlink($dirPath . $photo->path);
                    }
                }

                $this->property->addresses()->delete();
                $this->property->guestInfo()->delete();
                $this->property->rooms()->delete();
                $this->property->availability()->delete();
                $this->property->pricing()->delete();
                $this->property->descriptions()->delete();
                $this->property->photos()->delete();
                $this->property->amenities()->detach();
                $this->property->tags()->detach();

                if ($this->property->seo) {
                    $this->property->seo()->delete();
                }

                if (!$this->property->delete()) {
                    errPropertyDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->property)
                    ->setType(ActivityType::PROPERTY)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property: " . $this->property->nickname);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    private function relations(): array
    {
        return [
            'type', 'addresses', 'guestInfo', 'rooms.roomType', 'rooms.bedType',
            'availability', 'pricing', 'descriptions', 'photos', 'amenities', 'tags', 'features', 'seo',
        ];
    }

    private function syncNested(Request $request)
    {
        if ($request->has('addresses') && is_array($request->addresses)) {
            $this->property->addresses()->delete();
            foreach ($request->addresses as $address) {
                PropertyAddress::create(['propertyId' => $this->property->id] + $address);
            }
        }

        if ($request->has('guestInfo') && is_array($request->guestInfo)) {
            PropertyGuestInfo::updateOrCreate(
                ['propertyId' => $this->property->id],
                $request->guestInfo
            );
        }

        if ($request->has('rooms') && is_array($request->rooms)) {
            $this->property->rooms()->delete();
            foreach ($request->rooms as $room) {
                PropertyRoom::create(['propertyId' => $this->property->id] + $room);
            }
        }

        if ($request->has('availability') && is_array($request->availability)) {
            PropertyAvailability::updateOrCreate(
                ['propertyId' => $this->property->id],
                $request->availability
            );
        }

        if ($request->has('pricing') && is_array($request->pricing)) {
            PropertyPricing::updateOrCreate(
                ['propertyId' => $this->property->id],
                $request->pricing
            );
        }

        if ($request->has('descriptions') && is_array($request->descriptions)) {
            $this->property->descriptions()->delete();
            foreach ($request->descriptions as $description) {
                PropertyDescription::create(['propertyId' => $this->property->id] + $description);
            }
        }

        if ($request->has('amenityIds') && is_array($request->amenityIds)) {
            $this->property->amenities()->sync($request->amenityIds);
        }

        if ($request->has('tagIds') && is_array($request->tagIds)) {
            $this->property->tags()->sync($request->tagIds);
        }

        if ($request->has('features') && is_array($request->features)) {
            $syncData = [];
            foreach ($request->features as $feature) {
                $syncData[$feature['featureId']] = ['value' => $feature['value'] ?? null];
            }
            $this->property->features()->sync($syncData);
        }
    }
}