<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\SettingPropertyFeature;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingPropertyFeatureAlgo
{
    public function __construct(protected SettingPropertyFeature|int|null $propertyFeature = null)
    {
        if (is_int($this->propertyFeature)) {
            $this->propertyFeature = SettingPropertyFeature::find($this->propertyFeature);

            if (!$this->propertyFeature) {
                errPropertyFeatureGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->propertyFeature = SettingPropertyFeature::create(
                    $this->attributes($request) + created_by()
                );

                if (!$this->propertyFeature) {
                    errPropertyFeatureSave();
                }

                $this->uploadIcon($request);

                activity()->setCausedBy()
                    ->setReference($this->propertyFeature)
                    ->setType(ActivityType::PROPERTY_FEATURE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property feature: " . $this->propertyFeature->name);
            });

            return success($this->propertyFeature->fresh());

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->propertyFeature->update($this->attributes($request));

                $this->uploadIcon($request);

                activity()->setCausedBy()
                    ->setReference($this->propertyFeature)
                    ->setType(ActivityType::PROPERTY_FEATURE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property feature: " . $this->propertyFeature->name);
            });

            return success($this->propertyFeature->fresh());

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->propertyFeature->delete()) {
                    errPropertyFeatureDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyFeature)
                    ->setType(ActivityType::PROPERTY_FEATURE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property feature: " . $this->propertyFeature->name);
            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * Only the columns of the feature. Unknown keys sent by the form are ignored, and an
     * empty "order" / "hasValue" keeps the current value (or the default on create).
     */
    private function attributes(Request $request): array
    {
        return array_filter(
            $request->only(['name', 'hasValue', 'order']),
            fn ($value) => $value !== null && $value !== ''
        );
    }

    private function uploadIcon(Request $request): void
    {
        $dirPath = PathConstant::IMAGES_PROPERTY_FEATURE_STORAGE_PUBLIC_PATH();

        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {

            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $this->removeIconFile($dirPath);

            $image = $request->file('icon');
            $filename = filename($image, $this->propertyFeature->name);

            $image->move($dirPath, $filename);

            $this->propertyFeature->icon = $filename;
            $this->propertyFeature->save();

        } elseif ($request->boolean('deleteIcon')) {

            $this->removeIconFile($dirPath);

            $this->propertyFeature->icon = null;
            $this->propertyFeature->save();
        }
    }

    private function removeIconFile(string $dirPath): void
    {
        if (
            $this->propertyFeature->icon &&
            file_exists($dirPath . $this->propertyFeature->icon)
        ) {
            unlink($dirPath . $this->propertyFeature->icon);
        }
    }
}
