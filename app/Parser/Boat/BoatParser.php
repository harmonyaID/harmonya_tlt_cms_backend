<?php

namespace App\Parser\Boat;

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
            'boatComponentType'   => $boatComponentType,
            'description'         => $data->description,
            'promoPhotos'         => $promoPhotos,
            'priceFile'           => $data->priceFile
                ? Storage::disk('public')->url(PathConstant::FILES_BOAT . $data->priceFile)
                : null,
            'photos'              => $photos,
            'customInformations'  => $customInformations,
            'isActive'            => $data->isActive,
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

        foreach ($data->promoPhotos ?? [] as $photo) {

            $promoPhotos[] = [
                'id'   => $photo['id'],
                'file' => Storage::disk('public')->url(
                    PathConstant::IMAGES_BOAT_PROMO . $photo['file']
                ),
            ];
        }

        return [
            'id'                  => $data->id,
            'boatComponentTypeId'   => $data->boatComponentTypeId,
            'boatComponentTypeName' => optional($data->type)->name,
            'promoPhotos'         => $promoPhotos,
            'photos'              => $photos,
            'isActive'            => $data->isActive,
            'createdAt'           => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
