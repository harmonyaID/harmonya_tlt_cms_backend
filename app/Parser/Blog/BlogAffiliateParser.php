<?php

namespace App\Parser\Blog;

use Logia\Core\Parser\BaseParser;

class BlogAffiliateParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'bannerImage' => $data->bannerImageUrl(),
            'bannerUrl' => $data->bannerUrl,
            'isAppliedToAll' => $data->isAppliedToAll,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'bannerImage' => $data->bannerImageUrl(),
            'bannerUrl' => $data->bannerUrl,
            'isAppliedToAll' => $data->isAppliedToAll,
        ];
    }
}
