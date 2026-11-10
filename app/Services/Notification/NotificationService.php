<?php

namespace App\Services\Notification;

use App\Jobs\SendPushNotificationJob;
use App\Models\Notification\NotificationSetting;
use App\Models\Notification\StaffNotification;
use App\Models\Staff\StaffUser;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Notifies every active staff user (in-app and/or push, per the type's setting).
     * Never throws: a notification problem must not break the form that caused it.
     */
    public function notifyStaff(string $type, string $title, ?string $body = null, ?Model $ref = null, array $data = []): void
    {
        try {
            $setting = NotificationSetting::forType($type);

            if (!$setting->isActive || (!$setting->sendInApp && !$setting->sendPush)) {
                return;
            }

            $userIds = StaffUser::whereHas('account', fn ($q) => $q->where('isActive', true))->pluck('id')->all();

            if (!$userIds) {
                return;
            }

            $payload = ['type' => $type] + ($ref ? ['refType' => class_basename($ref), 'refId' => $ref->getKey()] : []) + $data;

            if ($setting->sendInApp) {
                foreach ($userIds as $userId) {
                    StaffNotification::create([
                        'staffUserId' => $userId,
                        'type' => $type,
                        'title' => $title,
                        'body' => $body,
                        'refType' => $ref ? class_basename($ref) : null,
                        'refId' => $ref?->getKey(),
                        'data' => $data ?: null,
                    ]);
                }
            }

            if ($setting->sendPush) {
                SendPushNotificationJob::dispatch($userIds, $title, (string) $body, $payload);
            }
        } catch (\Throwable $e) {
            logger()->error('Notification failed: ' . $e->getMessage());
        }
    }
}
