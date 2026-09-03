<?php

namespace App\Algorithms\Property;

use App\Models\Property\PropertyRoomType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyRoomTypeAlgo
{
    public function __construct(protected PropertyRoomType|int|null $propertyRoomType = null)
    {
        if (is_int($this->propertyRoomType)) {
            $this->propertyRoomType = PropertyRoomType::find($this->propertyRoomType);
            if (!$this->propertyRoomType) {
                errPropertyRoomTypeGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyRoomType = PropertyRoomType::create($request->all() + created_by());
                if (!$this->propertyRoomType) {
                    errPropertyRoomTypeSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyRoomType)
                    ->setType(ActivityType::PROPERTY_ROOM_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property room type: " . $this->propertyRoomType->name);

            });

            return success($this->propertyRoomType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyRoomType->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->propertyRoomType)
                    ->setType(ActivityType::PROPERTY_ROOM_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property room type: " . $this->propertyRoomType->name);

            });

            return success($this->propertyRoomType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->propertyRoomType->delete()) {
                    errPropertyRoomTypeDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyRoomType)
                    ->setType(ActivityType::PROPERTY_ROOM_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property room type: " . $this->propertyRoomType->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}