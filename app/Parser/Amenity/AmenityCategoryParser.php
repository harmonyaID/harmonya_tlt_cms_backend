<?php

namespace App\Parser\Amenity;

use Logia\Core\Parser\BaseParser;

class AmenityCategoryParser extends BaseParser
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
            'name' => $data->name,
            'icon' => $data->icon,
            'order' => $data->order,
            'amenities' => AmenityParser::briefs($data->amenities),
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
            'name' => $data->name,
            'icon' => $data->icon,
            'order' => $data->order,
        ];
    }
}