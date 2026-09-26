<?php

namespace App\Parser\Experience;

use App\Parser\Blog\BlogParser;
use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class ExperienceAreaParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,

            'type' => optional($data->type)->only(
                'id',
                'name'
            ),

            'name' => $data->name,
            'description' => $data->description,

            'featuredImage' => $data->featuredImageUrl(),
            'mapImage' => $data->mapImageUrl(),
            'banner' => $data->bannerUrl(),

            'customInformations' => $data->customInformations,

            'experienceSection1Ids' =>
                $data->experienceSection1Ids,

            'experienceSection2Ids' =>
                $data->experienceSection2Ids,

            'propertyIds' =>
                $data->propertyIds,

            'blogIds' =>
                $data->blogIds,

            'experienceSection1' =>
                $data->getExperienceSection1Data()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ])
                    ->values(),

            'experienceSection2' =>
                $data->getExperienceSection2Data()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ])
                    ->values(),

            'properties' =>
                $data->getPropertyData()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'name' => $item->nickname,
                    ])
                    ->values(),

            'blogs' =>
                BlogParser::briefs(
                    $data->getBlogData()
                ),

            'seo' =>
                SeoParser::first($data->seo),

            'createdAt' =>
                optional($data->createdAt)
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

            'type' => optional($data->type)->only(
                'id',
                'name'
            ),

            'name' => $data->name,
            'description' => $data->description,

            'featuredImage' => $data->featuredImageUrl(),
            'mapImage' => $data->mapImageUrl(),
            'banner' => $data->bannerUrl(),

            'customInformations' => $data->customInformations,

            'experienceSection1Ids' =>
                $data->experienceSection1Ids,

            'experienceSection2Ids' =>
                $data->experienceSection2Ids,

            'propertyIds' =>
                $data->propertyIds,

            'blogIds' =>
                $data->blogIds,

            'experienceSection1' =>
                $data->getExperienceSection1Data()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ])
                    ->values(),

            'experienceSection2' =>
                $data->getExperienceSection2Data()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ])
                    ->values(),

            'properties' =>
                $data->getPropertyData()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ])
                    ->values(),

            'blogs' =>
                BlogParser::briefs(
                    $data->getBlogData()
                ),

            'seo' =>
                SeoParser::first($data->seo),
        ];
    }
}