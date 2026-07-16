<?php

namespace App\Algorithms\Property;

use App\Models\Property\PropertyType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyTypeAlgo
{
    public function __construct(protected PropertyType|int|null $propertyType = null)
    {
        if (is_int($this->propertyType)) {
            $this->propertyType = PropertyType::find($this->propertyType);
            if (!$this->propertyType) {
                errPropertyTypeGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyType = PropertyType::create($request->all() + created_by());
                if (!$this->propertyType) {
                    errPropertyTypeSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyType)
                    ->setType(ActivityType::PROPERTY_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property type: " . $this->propertyType->name);

            });

            return success($this->propertyType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyType->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->propertyType)
                    ->setType(ActivityType::PROPERTY_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property type: " . $this->propertyType->name);

            });

            return success($this->propertyType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->propertyType->delete()) {
                    errPropertyTypeDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyType)
                    ->setType(ActivityType::PROPERTY_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property type: " . $this->propertyType->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}