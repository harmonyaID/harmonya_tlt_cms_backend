<?php

namespace App\Parser\Setting;

use Logia\Core\Parser\BaseParser;

class SettingAnalyticsParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'key' => $data->key,
            'value' => $data->value,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'key' => $data->key,
            'value' => $data->value,
        ];
    }
}