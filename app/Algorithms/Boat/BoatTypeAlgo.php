<?php

namespace App\Algorithms\Boat;

use App\Models\Boat\BoatType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatTypeAlgo
{
    public function __construct(protected BoatType|int|null $boatType = null)
    {
        if (is_int($this->boatType)) {
            $this->boatType = BoatType::find($this->boatType);
            if (!$this->boatType) {
                errBoatTypeGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boatType = BoatType::create($request->all() + created_by());
                if (!$this->boatType) {
                    errBoatTypeSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->boatType)
                    ->setType(ActivityType::BOAT_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new boat type: " . $this->boatType->name);

            });

            return success($this->boatType->load('boat'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boatType->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->boatType)
                    ->setType(ActivityType::BOAT_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update boat type: " . $this->boatType->name);

            });

            return success($this->boatType->load('boat'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->boatType->delete()) {
                    errBoatTypeDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->boatType)
                    ->setType(ActivityType::BOAT_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete boat type: " . $this->boatType->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}