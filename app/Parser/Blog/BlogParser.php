<?php

namespace App\Parser\Blog;

use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class BlogParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        $tags = [];
        foreach ($data->tags as $tag) {
            $tags[] = ['id' => $tag->id, 'name' => $tag->name];
        }

        return [
            'id' => $data->id,
            'category' => optional($data->category)->only('id', 'name'),
            'title' => $data->title,
            'slug' => $data->slug,
            'thumbnail' => $data->thumbnailUrl(),
            'excerpt' => $data->excerpt,
            'content' => $data->content,
            'author' => $data->author,
            'tags' => $tags,
            'isActive' => $data->isActive,
            'publishedAt' => optional($data->publishedAt)->format('d/m/Y H:i'),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
            'seo' => SeoParser::first($data->seo),

        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        $tags = [];
        foreach ($data->tags as $tag) {
            $tags[] = ['id' => $tag->id, 'name' => $tag->name];
        }

        return [
            'id' => $data->id,
            'category' => optional($data->category)->only('id', 'name'),
            'title' => $data->title,
            'slug' => $data->slug,
            'thumbnail' => $data->thumbnailUrl(),
            'excerpt' => $data->excerpt,
            'author' => $data->author,
            'tags' => $tags,
            'isActive' => $data->isActive,
            'publishedAt' => optional($data->publishedAt)->format('d/m/Y H:i'),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
