<?php

namespace App\Parser\Homepage;

use App\Parser\Seo\SeoParser;
use Logia\Core\Parser\BaseParser;

class HomepageParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'value' => $data->resolvedValue(),
            'locale' => $data->locale,
            'seo' => SeoParser::first($data->seo),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
            'updatedAt' => optional($data->updatedAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'value' => $data->resolvedValue(),
            'locale' => $data->locale,
            'seo' => SeoParser::first($data->seo),
        ];
    }
}
