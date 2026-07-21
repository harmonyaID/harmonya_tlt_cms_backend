<?php

namespace App\Parser\Seo;

use Logia\Core\Parser\BaseParser;

class SeoParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'info' => $data->info,
            'title' => $data->title,
            'slug' => $data->slug,
            'description' => $data->description,
            'metaKeyword' => $data->metaKeyword,
            'canonicalUrl' => $data->canonicalUrl,
            'thumbnail' => $data->thumbnailUrl(),
            'robotIndex' => $data->robotIndex,
            'robotFollow' => $data->robotFollow,
            'structuredData' => $data->structuredData,
        ];
    }
}