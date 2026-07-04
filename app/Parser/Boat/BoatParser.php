<?php

namespace App\Parser\Boat;

use Logia\Core\Parser\BaseParser;

class BoatParser extends BaseParser
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
                'order' => $photo->order,
            ];
        }

        $types = [];
        foreach ($data->types as $type) {
            $types[] = BoatTypeParser::brief($type);
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'routeFrom' => $data->routeFrom,
            'routeTo' => $data->routeTo,
            'departureTimesFromBali' => $data->departureTimesFromBali,
            'departureTimesFromLembongan' => $data->departureTimesFromLembongan,
            'notes' => $data->notes,
            'discountPercentage' => $data->discountPercentage,
            'isActive' => $data->isActive,
            'photos' => $photos,
            'types' => $types,
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
                'order' => $photo->order,
            ];
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'routeFrom' => $data->routeFrom,
            'routeTo' => $data->routeTo,
            'departureTimesFromBali' => $data->departureTimesFromBali,
            'departureTimesFromLembongan' => $data->departureTimesFromLembongan,
            'discountPercentage' => $data->discountPercentage,
            'isActive' => $data->isActive,
            'photos' => $photos,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}