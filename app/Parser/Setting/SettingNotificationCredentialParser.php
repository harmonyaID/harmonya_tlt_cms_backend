<?php

namespace App\Parser\Setting;

use App\Services\Constant\Setting\NotificationProvider;
use App\Services\Constant\Setting\Provider;
use Logia\Core\Parser\BaseParser;

class SettingNotificationCredentialParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'providerId' => $data->providerId,
            'provider' => NotificationProvider::OPTION[$data->providerId] ?? null,
            'name' => $data->name,
            'credentials' => $data->credentials,
            'isActive' => $data->isActive,
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'providerId' => $data->providerId,
            'provider' => NotificationProvider::OPTION[$data->providerId] ?? null,
            'name' => $data->name,
            'isActive' => $data->isActive,
        ];
    }
}