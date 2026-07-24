<?php

namespace App\Parser\Setting;

use Logia\Core\Parser\BaseParser;

class SettingPropertyFeatureParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'icon' => $data->icon,
            'hasValue' => $data->hasValue,
            'order' => $data->order,
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
            'icon' => $data->icon,
            'hasValue' => $data->hasValue,
            'order' => $data->order,
        ];
    }
}
