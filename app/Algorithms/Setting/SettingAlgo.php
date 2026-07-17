<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\Setting;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingAlgo
{
    /**
     * @param Setting|int|null $setting
     */
    public function __construct(protected Setting|int|null $setting = null)
    {
        if (is_int($this->setting)) {
            $this->setting = Setting::find($this->setting);
            if (!$this->setting) {
                errSettingGet();
            }
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

                if (!$this->setting->update($request->only('value'))) {
                    errSettingUpdate();
                }

                activity()->setCausedBy()
                    ->setReference($this->setting)
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update setting: " . $this->setting->name);

            });

            return success($this->setting);

        } catch (\Error $error) {
            exception($error);
        }
    }
}
