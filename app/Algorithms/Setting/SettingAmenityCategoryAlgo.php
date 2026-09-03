<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\SettingAmenityCategory;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingAmenityCategoryAlgo
{
    /**
     * @param SettingAmenityCategory|int|null $amenityCategory
     */
    public function __construct(protected SettingAmenityCategory|int|null $amenityCategory = null)
    {
        if (is_int($this->amenityCategory)) {
            $this->amenityCategory = SettingAmenityCategory::find($this->amenityCategory);
            if (!$this->amenityCategory) {
                errAmenityCategoryGet();
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

                $this->amenityCategory = SettingAmenityCategory::create($request->all() + created_by());
                if (!$this->amenityCategory) {
                    errAmenityCategorySave();
                }

                activity()->setCausedBy()
                    ->setReference($this->amenityCategory)
                    ->setType(ActivityType::AMENITY_CATEGORY)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new amenity category: " . $this->amenityCategory->name);

            });

            return success($this->amenityCategory);

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

                $this->amenityCategory->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->amenityCategory)
                    ->setType(ActivityType::AMENITY_CATEGORY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update amenity category: " . $this->amenityCategory->name);

            });

            return success($this->amenityCategory);

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

                if (!$this->amenityCategory->delete()) {
                    errAmenityCategoryDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->amenityCategory)
                    ->setType(ActivityType::AMENITY_CATEGORY)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete amenity category: " . $this->amenityCategory->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}