<?php

namespace App\Parser\IslandGuide;

use App\Parser\Page\PageParser;
use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class IslandGuideTypeParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'name' => $data->name,
            'excerpt' => $data->excerpt,
            'description' => $data->description,
            'featuredImage' => $data->featuredImageUrl(),
            'banner' => $data->bannerUrl(),
            'isPage' => $data->isPage,
            'pageReference' => PageParser::first($data->page),
            'seo' => SeoParser::first($data->seo),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'name' => $data->name,
            'excerpt' => $data->excerpt,
            'description' => $data->description,
            'featuredImage' => $data->featuredImageUrl(),
            'banner' => $data->bannerUrl(),
            'isPage' => $data->isPage,
            'pageReference' => PageParser::first($data->page),
            'seo' => SeoParser::first($data->seo),

        ];
    }
}
