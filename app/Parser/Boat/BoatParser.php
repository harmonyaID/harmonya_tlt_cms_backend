<?php

namespace App\Parser\Boat;

use App\Parser\Seo\SeoParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\Storage;
use Logia\Core\Parser\BaseParser;

class BoatParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id'    => $photo->id,
                'photo' => $photo->photoUrl(),
                'order' => $photo->order,
            ];
        }

        $customInformations = [];
        foreach ($data->customInformations as $info) {
            $customInformations[] = [
                'id'    => $info->id,
                'name'  => $info->name,
                'value' => $info->value,
                'order' => $info->order,
            ];
        }

        $promoPhotos = [];

        foreach ($data->promoPhotos ?? [] as $photo) {

            $promoPhotos[] = [
                'id'   => $photo['id'],
                'file' => Storage::disk('public')->url(
                    PathConstant::IMAGES_BOAT_PROMO . $photo['file']
                ),
            ];
        }
        $boatComponentType = null;

        if (isset($data->type)) {
            $boatComponentType = [
                'id'   => $data->boatComponentTypeId,
                'name' => optional($data->type)->name,
            ];
        }

        return [
            'id'                  => $data->id,
            'name'                  => $data->name,
            'boatComponentType'   => $boatComponentType,
            'description'         => $data->description,
            'promoPhotos'         => $promoPhotos,
            'priceFile'           => $data->priceFile
                ? Storage::disk('public')->url(PathConstant::FILES_BOAT . $data->priceFile)
                : null,
            'photos'              => $photos,
            'customInformations'  => $customInformations,
            'locale'              => $data->locale,
            'isActive'            => $data->isActive,
            'seo'                 => SeoParser::first($data->seo),
            'createdAt'           => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id'    => $photo->id,
                'photo' => $photo->photoUrl(),
                'order' => $photo->order,
            ];
        }

        $promoPhotos = [];

        foreach ($data->promoPhotos ?? [] as $key => $photo) {
            if (is_array($photo)) {
                $promoPhotos[] = [
                    'id'   => $photo['id'] ?? $key,
                    'file' => Storage::disk('public')->url(
                        PathConstant::IMAGES_BOAT_PROMO . $photo['file']
                    ),
                ];
            } else {
                $promoPhotos[] = [
                    'id'   => $key,
                    'file' => Storage::disk('public')->url(
                        PathConstant::IMAGES_BOAT_PROMO . $photo
                    ),
                ];
            }
        }

        return [
            'id'                  => $data->id,
            'name'                  => $data->name,
            'boatComponentTypeId'   => $data->boatComponentTypeId,
            'boatComponentTypeName' => optional($data->type)->name,
            'promoPhotos'         => $promoPhotos,
            'photos'              => $photos,
            'locale'              => $data->locale,
            'isActive'            => $data->isActive,
            'createdAt'           => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}