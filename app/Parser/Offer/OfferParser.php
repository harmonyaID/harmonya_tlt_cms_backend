<?php

namespace App\Parser\Offer;

use App\Parser\Acf\AcfParser;
use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class OfferParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        $properties = [];
        foreach ($data->properties as $property) {
            $properties[] = ['id' => $property->id, 'nickname' => $property->nickname];
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'slug' => $data->slug,
            'thumbnail' => $data->thumbnailUrl(),
            'excerpt' => $data->excerpt,
            'content' => $data->content,
            'properties' => $properties,
            'startDate' => optional($data->startDate)->format('d/m/Y'),
            'endDate' => optional($data->endDate)->format('d/m/Y'),
            'locale' => $data->locale,
            'isActive' => $data->isActive,
            'publishedAt' => optional($data->publishedAt)->format('d/m/Y H:i'),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
            'seo' => SeoParser::first($data->seo),
            'acf' => AcfParser::forContent($data->acf),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'slug' => $data->slug,
            'thumbnail' => $data->thumbnailUrl(),
            'excerpt' => $data->excerpt,
            'propertyCount' => $data->properties->count(),
            'startDate' => optional($data->startDate)->format('d/m/Y'),
            'endDate' => optional($data->endDate)->format('d/m/Y'),
            'locale' => $data->locale,
            'isActive' => $data->isActive,
            'publishedAt' => optional($data->publishedAt)->format('d/m/Y H:i'),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
