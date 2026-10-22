<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\SettingAmenity;
use App\Parser\Setting\SettingAmenityParser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingAmenityAlgo
{
    /**
     * @param SettingAmenity|int|null $amenity
     */
    public function __construct(protected SettingAmenity|int|null $amenity = null)
    {
        if (is_int($this->amenity)) {
            $this->amenity = SettingAmenity::find($this->amenity);
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

                $this->amenity = SettingAmenity::create($request->except('icon') + created_by());
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

                $this->amenity->update($request->except('icon'));

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

    /**
     * Update only the icon image of the amenity.
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

                $dirPath = PathConstant::IMAGES_AMENITY_STORAGE_PUBLIC_PATH();

                if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
                    if (!file_exists($dirPath)) {
                        mkdir($dirPath, 0777, true);
                    }

                    $this->removeIconFile($dirPath);

                    $image = $request->file('icon');
                    $filename = filename($image, $this->amenity->name);
                    $image->move($dirPath, $filename);

                    $this->amenity->icon = $filename;
                } elseif ($request->boolean('deleteIcon')) {
                    $this->removeIconFile($dirPath);
                    $this->amenity->icon = null;
                }

                $this->amenity->save();

                activity()->setCausedBy()
                    ->setReference($this->amenity)
                    ->setType(ActivityType::AMENITY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update amenity icon: " . $this->amenity->name);

            });

            return success(SettingAmenityParser::first($this->amenity->fresh()));

        } catch (\Error $error) {
            exception($error);
        }
    }

    private function removeIconFile(string $dirPath): void
    {
        if ($this->amenity->icon && file_exists($dirPath . $this->amenity->icon)) {
            unlink($dirPath . $this->amenity->icon);
        }
    }
}
