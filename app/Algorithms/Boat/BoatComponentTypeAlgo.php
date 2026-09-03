<?php

namespace App\Algorithms\Boat;

use App\Models\Boat\BoatComponentType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatComponentTypeAlgo
{
    public function __construct(protected BoatComponentType|int|null $boatComponentType = null)
    {
        if (is_int($this->boatComponentType)) {
            $this->boatComponentType = BoatComponentType::find($this->boatComponentType);
            if (!$this->boatComponentType) {
                errBoatComponentTypeGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boatComponentType = BoatComponentType::create($request->all() + created_by());
                if (!$this->boatComponentType) {
                    errBoatComponentTypeSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->boatComponentType)
                    ->setType(ActivityType::BOAT_COMPONENT_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new boat component type: " . $this->boatComponentType->name);

            });

            return success($this->boatComponentType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boatComponentType->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->boatComponentType)
                    ->setType(ActivityType::BOAT_COMPONENT_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update boat component type: " . $this->boatComponentType->name);

            });

            return success($this->boatComponentType);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->boatComponentType->delete()) {
                    errBoatComponentTypeDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->boatComponentType)
                    ->setType(ActivityType::BOAT_COMPONENT_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete boat component type: " . $this->boatComponentType->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}