<?php

namespace App\Parser\Experience;

use Logia\Core\Parser\BaseParser;

class ExperienceParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = ['id' => $photo->id, 'photo' => $photo->photoUrl(), 'order' => $photo->order];
        }

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'category' => optional($data->category)->only('id', 'name'),
            'name' => $data->name,
            'description' => $data->description,
            'thumbnail' => $data->thumbnailUrl(),
            'catalogPdf' => $data->catalogPdfUrl(),
            'openHours' => $data->openHours,
            'mapEmbedUrl' => $data->mapEmbedUrl,
            'mapLocationUrl' => $data->mapLocationUrl,
            'whatsapp' => $data->whatsapp,
            'instagram' => $data->instagram,
            'website' => $data->website,
            'isActive' => $data->isActive,
            'photos' => $photos,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'category' => optional($data->category)->only('id', 'name'),
            'name' => $data->name,
            'thumbnail' => $data->thumbnailUrl(),
            'openHours' => $data->openHours,
            'whatsapp' => $data->whatsapp,
            'instagram' => $data->instagram,
            'isActive' => $data->isActive,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}