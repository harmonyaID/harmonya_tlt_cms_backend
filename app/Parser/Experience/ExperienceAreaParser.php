<?php

namespace App\Parser\Experience;

use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class ExperienceAreaParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'name' => $data->name,
            'description' => $data->description,

            'featuredImage' => $data->featuredImageUrl(),
            'mapsImage' => $data->mapsImageUrl(),
            'banner' => $data->bannerUrl(),

            'customInformations' => $data->customInformations,

            'experiencePlayIds' => $data->experiencePlayIds,
            'experienceEatIds' => $data->experienceEatIds,
            'propertyIds' => $data->propertyIds,

            'experiencePlay' => $data->getExperiencePlayData()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])
                ->values(),

            'experienceEat' => $data->getExperienceEatData()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])
                ->values(),

            'properties' => $data->getPropertyData()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])
                ->values(),

            'seo' => SeoParser::first($data->seo),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'name' => $data->name,
            'description' => $data->description,

            'featuredImage' => $data->featuredImageUrl(),
            'mapsImage' => $data->mapsImageUrl(),
            'banner' => $data->bannerUrl(),

            'customInformations' => $data->customInformations,

            'experiencePlayIds' => $data->experiencePlayIds,
            'experienceEatIds' => $data->experienceEatIds,
            'propertyIds' => $data->propertyIds,

            'experiencePlay' => $data->getExperiencePlayData()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])
                ->values(),

            'experienceEat' => $data->getExperienceEatData()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])
                ->values(),

            'properties' => $data->getPropertyData()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])
                ->values(),

            'seo' => SeoParser::first($data->seo),
        ];
    }
}