<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\SettingAnalytic;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingAnalyticsAlgo
{
    public function __construct(protected SettingAnalytic|int|null $analytics = null)
    {
        if (is_int($this->analytics)) {
            $this->analytics = SettingAnalytic::find($this->analytics);

            if (!$this->analytics) {
                errSettingAnalyticsGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                if (SettingAnalytic::where('key', $request->key)->exists()) {
                    errSettingAnalyticsKeyExists();
                }

                $this->analytics = SettingAnalytic::create([
                    'name' => $request->name,
                    'key' => $request->key,
                    'value' => $request->value,
                ] + created_by());

                if (!$this->analytics) {
                    errSettingAnalyticsSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->analytics)
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new setting analytics: " . $this->analytics->name);
            });

            return success($this->analytics);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $exists = SettingAnalytic::where('key', $request->key)
                    ->where('id', '!=', $this->analytics->id)
                    ->exists();

                if ($exists) {
                    errSettingAnalyticsKeyExists();
                }

                $this->analytics->update([
                    'name' => $request->name,
                    'key' => $request->key,
                    'value' => $request->value,
                ]);

                activity()->setCausedBy()
                    ->setReference($this->analytics)
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update setting analytics: " . $this->analytics->name);
            });

            return success($this->analytics);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->analytics->delete()) {
                    errSettingAnalyticsDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->analytics)
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete setting analytics: " . $this->analytics->name);
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }
}