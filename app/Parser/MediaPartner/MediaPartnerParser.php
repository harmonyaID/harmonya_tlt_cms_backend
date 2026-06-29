<?php

namespace App\Parser\MediaPartner;

use Logia\Core\Parser\BaseParser;

class MediaPartnerParser extends BaseParser
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
            'description' => $data->description,
            'image' => $data->imageUrl(),
            'url' => $data->url,
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
            'name' => $data->name,
            'description' => $data->description,
            'image' => $data->imageUrl(),
            'url' => $data->url,
            'isPublish' => $data->isPublish,

        ];
    }
}