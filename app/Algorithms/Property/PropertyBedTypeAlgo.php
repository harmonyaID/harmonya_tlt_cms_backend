<?php

namespace App\Algorithms\Property;

use App\Models\Property\PropertyBedType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyBedTypeAlgo
{
    public function __construct(protected PropertyBedType|int|null $propertyBedType = null)
    {
        if (is_int($this->propertyBedType)) {
            $this->propertyBedType = PropertyBedType::find($this->propertyBedType);
            if (!$this->propertyBedType) {
                errPropertyBedTypeGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyBedType = PropertyBedType::create($request->all() + created_by());
                if (!$this->propertyBedType) {
                    errPropertyBedTypeSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyBedType)
                    ->setType(ActivityType::PROPERTY_BED_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property bed type: " . $this->propertyBedType->name);

            });

            return success($this->propertyBedType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyBedType->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->propertyBedType)
                    ->setType(ActivityType::PROPERTY_BED_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property bed type: " . $this->propertyBedType->name);

            });

            return success($this->propertyBedType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->propertyBedType->delete()) {
                    errPropertyBedTypeDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyBedType)
                    ->setType(ActivityType::PROPERTY_BED_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property bed type: " . $this->propertyBedType->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}