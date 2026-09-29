<?php

namespace App\Parser\Boat;

use App\Parser\Acf\AcfParser;
use App\Parser\Seo\SeoParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\Storage;
use Logia\Core\Parser\BaseParser;

class BoatParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,

            'promoLabel' => $data->promoLabel,
            'promoLabelUrl' => $data->promoLabelUrl,

            'discountLabel' => $data->discountLabel,
            'discountLabelUrl' => $data->discountLabelUrl,

            'schedule' => $data->schedule ?? [],

            'boatComponentType' => self::boatComponentType($data),

            'description' => $data->description,

            'promoPhotos' => self::promoPhotos($data),

            'priceFiles' => self::priceFiles($data),

            'mapImage' => $data->mapImage
                ? Storage::disk('public')->url(
                    PathConstant::IMAGES_BOAT_MAPS . $data->mapImage
                )
                : null,

            'photos' => self::photos($data),

            'customInformations' => self::customInformations($data),

            'locale' => $data->locale,

            'isActive' => $data->isActive,

            'seo' => SeoParser::first($data->seo),

            'acf' => AcfParser::forContent($data->acf),

            'createdAt' => optional($data->createdAt)
                ->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,

            'promoLabel' => $data->promoLabel,
            'promoLabelUrl' => $data->promoLabelUrl,

            'discountLabel' => $data->discountLabel,
            'discountLabelUrl' => $data->discountLabelUrl,

            'schedule' => $data->schedule ?? [],

            'boatComponentTypeId' => $data->boatComponentTypeId,

            'boatComponentTypeName' => optional($data->type)->name,

            'promoPhotos' => self::promoPhotos($data),

            'priceFiles' => self::priceFiles($data),

            'mapImage' => $data->mapImage
                ? Storage::disk('public')->url(
                    PathConstant::IMAGES_BOAT_MAPS . $data->mapImage
                )
                : null,

            'photos' => self::photos($data),

            'locale' => $data->locale,

            'isActive' => $data->isActive,

            'createdAt' => optional($data->createdAt)
                ->format('d/m/Y H:i'),
        ];
    }

    private static function photos($data): array
    {
        $photos = [];

        foreach ($data->photos ?? [] as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'photo' => $photo->photoUrl(),
                'order' => $photo->order,
            ];
        }

        return $photos;
    }

    private static function promoPhotos($data): array
    {
        $promoPhotos = [];

        foreach ($data->promoPhotos ?? [] as $key => $photo) {
            if (is_array($photo)) {
                $file = $photo['file'] ?? null;

                if (!$file) {
                    continue;
                }

                $promoPhotos[] = [
                    'id' => $photo['id'] ?? $key,
                    'file' => Storage::disk('public')->url(
                        PathConstant::IMAGES_BOAT_PROMO . $file
                    ),
                ];

                continue;
            }

            if (!$photo) {
                continue;
            }

            $promoPhotos[] = [
                'id' => $key,
                'file' => Storage::disk('public')->url(
                    PathConstant::IMAGES_BOAT_PROMO . $photo
                ),
            ];
        }

        return $promoPhotos;
    }

    private static function priceFiles($data): array
    {
        $priceFiles = [];

        foreach ($data->priceFiles ?? [] as $key => $priceFile) {
            if (is_array($priceFile)) {
                $file = $priceFile['file'] ?? null;

                if (!$file) {
                    continue;
                }

                $priceFiles[] = [
                    'id' => $priceFile['id'] ?? $key,
                    'file' => Storage::disk('public')->url(
                        PathConstant::FILES_BOAT . $file
                    ),
                ];

                continue;
            }

            if (!$priceFile) {
                continue;
            }

            $priceFiles[] = [
                'id' => $key,
                'file' => Storage::disk('public')->url(
                    PathConstant::FILES_BOAT . $priceFile
                ),
            ];
        }

        return $priceFiles;
    }

    private static function customInformations($data): array
    {
        if (!$data->customInformations) {
            return [];
        }

        return $data->customInformations
            ->groupBy('groupName')
            ->map(function ($items, $groupName) {
                return [
                    'name' => $groupName,
                    'customInformations' => $items
                        ->sortBy('order')
                        ->values()
                        ->map(function ($info) {
                            return [
                                'id' => $info->id,
                                'name' => $info->name,
                                'value' => $info->value,
                                'order' => $info->order,
                            ];
                        })
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    private static function boatComponentType($data): ?array
    {
        if (!$data->type) {
            return null;
        }

        return [
            'id' => $data->boatComponentTypeId,
            'name' => $data->type->name,
        ];
    }
}