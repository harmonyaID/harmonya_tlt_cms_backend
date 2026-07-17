<?php

namespace App\Parser\Property;

use Logia\Core\Parser\BaseParser;

class PropertyReviewParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'photo' => $photo->photoUrl(),
            ];
        }

        return [
            'id' => $data->id,
            'propertyId' => $data->propertyId,
            'property' => optional($data->property)->only('id', 'nickname'),
            'name' => $data->name,
            'rating' => $data->rating,
            'review' => $data->review,
            'isActive' => $data->isActive,
            'photos' => $photos,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'photo' => $photo->photoUrl(),
            ];
        }

        return [
            'id' => $data->id,
            'propertyId' => $data->propertyId,
            'name' => $data->name,
            'rating' => $data->rating,
            'review' => $data->review,
            'isActive' => $data->isActive,
            'photos' => $photos,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
