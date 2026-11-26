<?php

namespace App\Algorithms\Property;

use App\Models\Property\PropertyArea;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyAreaAlgo
{
    public function __construct(protected PropertyArea|int|null $propertyArea = null)
    {
        if (is_int($this->propertyArea)) {
            $this->propertyArea = PropertyArea::find($this->propertyArea);
            if (!$this->propertyArea) {
                errPropertyAreaGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyArea = PropertyArea::create($request->all() + created_by());
                if (!$this->propertyArea) {
                    errPropertyAreaSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyArea)
                    ->setType(ActivityType::PROPERTY_AREA)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property area: " . $this->propertyArea->name);

            });

            return success($this->propertyArea);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyArea->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->propertyArea)
                    ->setType(ActivityType::PROPERTY_AREA)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property area: " . $this->propertyArea->name);

            });

            return success($this->propertyArea);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->propertyArea->delete()) {
                    errPropertyAreaDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyArea)
                    ->setType(ActivityType::PROPERTY_AREA)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property area: " . $this->propertyArea->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}