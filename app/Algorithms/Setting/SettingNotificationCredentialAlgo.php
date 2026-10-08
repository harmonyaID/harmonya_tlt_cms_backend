<?php

namespace App\Algorithms\Setting;

use App\Models\Setting\SettingNotificationCredential;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingNotificationCredentialAlgo
{
    /**
     * @param SettingNotificationCredential|int|null $settingNotificationCredential
     */
    public function __construct(
        protected SettingNotificationCredential|int|null $settingNotificationCredential = null
    ) {
        if (is_int($this->settingNotificationCredential)) {
            $this->settingNotificationCredential =
                SettingNotificationCredential::find(
                    $this->settingNotificationCredential
                );

            if (!$this->settingNotificationCredential) {
                errSettingNotificationCredentialGet();
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

                $this->settingNotificationCredential =
                    SettingNotificationCredential::create(
                        $request->only([
                            'providerId',
                            'name',
                            'credentials',
                            'isActive',
                        ])
                    );

                if (!$this->settingNotificationCredential) {
                    errSettingNotificationCredentialSave();
                }

                activity()
                    ->setCausedBy()
                    ->setReference(
                        $this->settingNotificationCredential
                    )
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::CREATE)
                    ->log(
                        'Create setting notification credential: '
                        . $this->settingNotificationCredential->name
                    );
            });

            return success($this->settingNotificationCredential);

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

                if (!$this->settingNotificationCredential->update(
                    $request->only([
                        'providerId',
                        'name',
                        'credentials',
                        'isActive',
                    ])
                )) {
                    errSettingNotificationCredentialUpdate();
                }

                activity()
                    ->setCausedBy()
                    ->setReference(
                        $this->settingNotificationCredential
                    )
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::UPDATE)
                    ->log(
                        'Update setting notification credential: '
                        . $this->settingNotificationCredential->name
                    );
            });

            return success($this->settingNotificationCredential);

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

                if (!$this->settingNotificationCredential->delete()) {
                    errSettingNotificationCredentialDelete();
                }

                activity()
                    ->setCausedBy()
                    ->setReference(
                        $this->settingNotificationCredential
                    )
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::DELETE)
                    ->log(
                        'Delete setting notification credential: '
                        . $this->settingNotificationCredential->name
                    );
            });

            return success($this->settingNotificationCredential);

        } catch (\Error $error) {
            exception($error);
        }
    }
}