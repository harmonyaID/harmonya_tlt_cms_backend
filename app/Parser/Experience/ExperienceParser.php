<?php

namespace App\Parser\Experience;

use App\Parser\Acf\AcfParser;
use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class ExperienceParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'photo' => $photo->photoUrl(),
                'order' => $photo->order,
            ];
        }

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'area' => optional($data->area)->only('id', 'name'),
            'name' => $data->name,
            'openHours' => $data->openHours,
            'description' => $data->description,
            'thumbnail' => $data->thumbnailUrl(),
            'mapImage' => $data->mapImageUrl(),
            'mapLocationUrl' => $data->mapLocationUrl,
            'whatsapp' => $data->whatsapp,
            'instagram' => $data->instagram,
            'website' => $data->website,
            'catalogs' => $data->catalogsWithUrl(),
            'locale' => $data->locale,
            'isActive' => $data->isActive,
            'showInquiry' => $data->showInquiry,
            'photos' => $photos,
            'seo' => SeoParser::first($data->seo),
            'acf' => AcfParser::forContent($data->acf),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'area' => optional($data->area)->only('id', 'name'),
            'name' => $data->name,
            'openHours' => $data->openHours,
            'thumbnail' => $data->thumbnailUrl(),
            'whatsapp' => $data->whatsapp,
            'instagram' => $data->instagram,
            'locale' => $data->locale,
            'isActive' => $data->isActive,
            'showInquiry' => $data->showInquiry,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}