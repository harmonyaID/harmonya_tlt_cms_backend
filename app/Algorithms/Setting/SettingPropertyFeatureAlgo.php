<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\SettingPropertyFeature;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
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

                $this->propertyFeature = SettingPropertyFeature::create($request->all() + created_by());
                if (!$this->propertyFeature) {
                    errPropertyFeatureSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyFeature)
                    ->setType(ActivityType::PROPERTY_FEATURE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property feature: " . $this->propertyFeature->name);
            });

            return success($this->propertyFeature);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->propertyFeature->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->propertyFeature)
                    ->setType(ActivityType::PROPERTY_FEATURE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property feature: " . $this->propertyFeature->name);
            });

            return success($this->propertyFeature);
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
}
