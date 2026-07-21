<?php

namespace App\Parser\Experience;

use Logia\Core\Parser\BaseParser;

class ExperienceTypeParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'featuredImage' => $data->featuredImageUrl(),
            'banner' => $data->bannerUrl(),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'featuredImage' => $data->featuredImageUrl(),
            'banner' => $data->bannerUrl(),
        ];
    }
}
