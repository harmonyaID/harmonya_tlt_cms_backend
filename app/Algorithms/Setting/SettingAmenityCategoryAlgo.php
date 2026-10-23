<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\SettingAmenityCategory;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use App\Parser\Setting\SettingAmenityCategoryParser;
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

                $this->amenityCategory = SettingAmenityCategory::create($request->except('icon') + created_by());
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

                $this->amenityCategory->update($request->except('icon'));

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

    /**
     * Update only the icon image of the amenity category.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function updateIcon(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $dirPath = PathConstant::IMAGES_AMENITY_CATEGORY_STORAGE_PUBLIC_PATH();

                if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
                    if (!file_exists($dirPath)) {
                        mkdir($dirPath, 0777, true);
                    }

                    $this->removeIconFile($dirPath);

                    $image = $request->file('icon');
                    $filename = filename($image, $this->amenityCategory->name);
                    $image->move($dirPath, $filename);

                    $this->amenityCategory->icon = $filename;
                } elseif ($request->boolean('deleteIcon')) {
                    $this->removeIconFile($dirPath);
                    $this->amenityCategory->icon = null;
                }

                $this->amenityCategory->save();

                activity()->setCausedBy()
                    ->setReference($this->amenityCategory)
                    ->setType(ActivityType::AMENITY_CATEGORY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update amenity category icon: " . $this->amenityCategory->name);

            });

            return success(SettingAmenityCategoryParser::first($this->amenityCategory->fresh()));

        } catch (\Error $error) {
            exception($error);
        }
    }

    private function removeIconFile(string $dirPath): void
    {
        if ($this->amenityCategory->icon && file_exists($dirPath . $this->amenityCategory->icon)) {
            unlink($dirPath . $this->amenityCategory->icon);
        }
    }
}
