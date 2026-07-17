<?php

namespace App\Parser\Setting;

use Logia\Core\Parser\BaseParser;

class SettingParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'value' => $data->value,
            'description' => $data->description,
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
            'value' => $data->value,
            'description' => $data->description,
        ];
    }
}
