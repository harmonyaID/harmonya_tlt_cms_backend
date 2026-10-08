<?php

namespace App\Parser\Blog;

use App\Parser\Acf\AcfParser;
use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class BlogParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        $categories = $data->categories->map(
            fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
            ]
        )->values();

        $tags = $data->tags->map(
            fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]
        )->values();

        return [
            'id' => $data->id,
            'categories' => $categories,
            'title' => $data->title,
            'slug' => $data->slug,
            'thumbnail' => $data->thumbnailUrl(),
            'promoBanner' => $data->promoBannerUrl(),
            'promoBannerUrl' => $data->promoBannerUrl,
            'excerpt' => $data->excerpt,
            'content' => $data->content,
            'author' => $data->author,
            'tags' => $tags,
            'properties' => $data->properties
                ->map(fn ($property) => [
                    'id' => $property->id,
                    'nickname' => $property->nickname,
                    'order' => $property->pivot->order,
                ])
                ->values(),
            'locale' => $data->locale,
            'visibility' => $data->visibility,
            'isActive' => $data->isActive,
            'publishedAt' => optional($data->publishedAt)
                ->format('d/m/Y H:i'),
            'createdAt' => optional($data->createdAt)
                ->format('d/m/Y H:i'),
            'seo' => SeoParser::first($data->seo),
            'acf' => AcfParser::forContent($data->acf),
            'uniqueVisitorCount' => $data->uniqueVisitorCount(),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        $categories = $data->categories->map(
            fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
            ]
        )->values();

        $tags = $data->tags->map(
            fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]
        )->values();

        return [
            'id' => $data->id,
            'categories' => $categories,
            'title' => $data->title,
            'slug' => $data->slug,
            'thumbnail' => $data->thumbnailUrl(),
            'promoBanner' => $data->promoBannerUrl(),
            'promoBannerUrl' => $data->promoBannerUrl,
            'content' => $data->content,
            'excerpt' => $data->excerpt,
            'author' => $data->author,
            'tags' => $tags,
            'properties' => $data->properties
                ->map(fn ($property) => [
                    'id' => $property->id,
                    'nickname' => $property->nickname,
                    'order' => $property->pivot->order,
                ])
                ->values(),
            'locale' => $data->locale,
            'visibility' => $data->visibility,
            'isActive' => $data->isActive,
            'publishedAt' => optional($data->publishedAt)
                ->format('d/m/Y H:i'),
            'createdAt' => optional($data->createdAt)
                ->format('d/m/Y H:i'),
            'uniqueVisitorCount' => $data->uniqueVisitorCount(),
        ];
    }
}