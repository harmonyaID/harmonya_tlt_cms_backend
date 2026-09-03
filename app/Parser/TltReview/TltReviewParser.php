<?php

namespace App\Parser\TltReview;

use Logia\Core\Parser\BaseParser;

class TltReviewParser extends BaseParser
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

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'photo' => $photo->photoUrl(),
            ];
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'position' => $data->position,
            'company' => $data->company,
            'rating' => $data->rating,
            'review' => $data->review,
            'isActive' => $data->isActive,
            'photos' => $photos,
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

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'photo' => $photo->photoUrl(),
            ];
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'position' => $data->position,
            'company' => $data->company,
            'rating' => $data->rating,
            'review' => $data->review,
            'isActive' => $data->isActive,
            'photos' => $photos,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}