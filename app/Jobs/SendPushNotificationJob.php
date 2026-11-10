<?php

namespace App\Jobs;

use App\Models\Notification\StaffUserDevice;
use App\Services\Notification\FcmClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $staffUserIds, public string $title, public string $body, public array $data = []) {}

    public function handle(FcmClient $fcm): void
    {
        if (!$fcm->isConfigured()) {
            throw new \RuntimeException('Firebase is not configured.');
        }

        StaffUserDevice::whereIn('staffUserId', $this->staffUserIds)->get()->each(function (StaffUserDevice $device) use ($fcm) {
            $sent = $fcm->send($device->fcmToken, $this->title, $this->body, $this->data);

            if ($sent === false) {
                $device->forceDelete();
            }
        });
    }
}
