<?php

namespace App\Algorithms\Property;

use App\Models\Property\PropertySourceType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertySourceTypeAlgo
{
    public function __construct(protected PropertySourceType|int|null $propertySourceType = null)
    {
        if (is_int($this->propertySourceType)) {
            $this->propertySourceType = PropertySourceType::find($this->propertySourceType);
            if (!$this->propertySourceType) {
                errPropertySourceTypeGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertySourceType = PropertySourceType::create($request->all() + created_by());
                if (!$this->propertySourceType) {
                    errPropertySourceTypeSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertySourceType)
                    ->setType(ActivityType::PROPERTY_SOURCE_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property source type: " . $this->propertySourceType->name);

            });

            return success($this->propertySourceType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertySourceType->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->propertySourceType)
                    ->setType(ActivityType::PROPERTY_SOURCE_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property source type: " . $this->propertySourceType->name);

            });

            return success($this->propertySourceType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->propertySourceType->delete()) {
                    errPropertySourceTypeDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertySourceType)
                    ->setType(ActivityType::PROPERTY_SOURCE_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property source type: " . $this->propertySourceType->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}
