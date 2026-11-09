<?php

namespace App\Algorithms\Setting;

use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationCredentialAlgo
{
    public function __construct(
        protected NotificationCredential|int|null $notificationCredential = null
    ) {
        if (is_int($this->notificationCredential)) {
            $this->notificationCredential = NotificationCredential::find(
                $this->notificationCredential
            );

            if (!$this->notificationCredential) {
                errNotificationCredentialGet();
            }
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                if (!$this->notificationCredential->update(
                    $request->only([
                        'providerId',
                        'name',
                        'credentials',
                        'isActive',
                    ])
                )) {
                    errNotificationCredentialUpdate();
                }

                activity()
                    ->setCausedBy()
                    ->setReference($this->notificationCredential)
                    ->setType(ActivityType::SETTING)
                    ->setAction(ActivityAction::UPDATE)
                    ->log(
                        "Update notification credential: "
                        . $this->notificationCredential->name
                    );
            });

            return success($this->notificationCredential);
        } catch (\Error $error) {
            exception($error);
        }
    }
}
