<?php

namespace App\Parser\Setting;

use Logia\Core\Parser\BaseParser;

class SettingAmenityParser extends BaseParser
{

    /**
     * @param $data
     *
     * @return array|null
     */
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'categoryId' => $data->categoryId,
            'category' => SettingAmenityCategoryParser::brief($data->category),
            'name' => $data->name,
            'icon' => $data->icon,
            'isPopular' => $data->isPopular,
            'order' => $data->order,
            'isPublish' => $data->isPublish,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param $data
     *
     * @return array|null
     */
    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'categoryId' => $data->categoryId,
            'name' => $data->name,
            'icon' => $data->icon,
            'isPopular' => $data->isPopular,
            'order' => $data->order,
            'isPublish' => $data->isPublish,
        ];
    }
}