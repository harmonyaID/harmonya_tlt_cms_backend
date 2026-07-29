<?php

namespace App\Parser\Experience;

use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class ExperienceAreaParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'name' => $data->name,
            'description' => $data->description,
            'featuredImage' => $data->featuredImageUrl(),
            'banner' => $data->bannerUrl(),
            'seo' => SeoParser::first($data->seo),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'name' => $data->name,
            'description' => $data->description,
            'featuredImage' => $data->featuredImageUrl(),
            'banner' => $data->bannerUrl(),
        ];
    }
}
