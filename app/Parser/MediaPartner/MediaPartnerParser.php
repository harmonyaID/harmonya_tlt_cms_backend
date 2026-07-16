<?php

namespace App\Parser\MediaPartner;

use App\Services\Constant\MediaPartner\MediaPartnerType;
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
            'featuredImage' => $data->featuredImageUrl(),
            'logo' => $data->logoUrl(),
            'url' => $data->url,
            'type' => MediaPartnerType::idName($data->typeId),
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
            'featuredImage' => $data->featuredImageUrl(),
            'logo' => $data->logoUrl(),
            'url' => $data->url,
            'isPublish' => $data->isPublish,
            'type' => MediaPartnerType::idName($data->typeId),
        ];
    }
}