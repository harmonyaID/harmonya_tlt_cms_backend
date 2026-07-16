<?php

namespace App\Algorithms\Amenity;

use App\Models\Amenity\Amenity;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmenityAlgo
{
    /**
     * @param Amenity|int|null $amenity
     */
    public function __construct(protected Amenity|int|null $amenity = null)
    {
        if (is_int($this->amenity)) {
            $this->amenity = Amenity::find($this->amenity);
            if (!$this->amenity) {
                errAmenityGet();
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->amenity = Amenity::create($request->all() + created_by());
                if (!$this->amenity) {
                    errAmenitySave();
                }

                activity()->setCausedBy()
                    ->setReference($this->amenity)
                    ->setType(ActivityType::AMENITY)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new amenity: " . $this->amenity->name);

            });

            return success($this->amenity);

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->amenity->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->amenity)
                    ->setType(ActivityType::AMENITY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update amenity: " . $this->amenity->name);

            });

            return success($this->amenity);

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->amenity->delete()) {
                    errAmenityDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->amenity)
                    ->setType(ActivityType::AMENITY)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete amenity: " . $this->amenity->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}