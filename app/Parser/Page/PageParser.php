<?php

namespace App\Parser\Page;

use App\Parser\Acf\AcfParser;
use App\Parser\Seo\SeoParser;
use App\Parser\Staff\StaffParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\Storage;
use Logia\Core\Parser\BaseParser;

class PageParser extends BaseParser
{
    /**
     * @param $data
     *
     * @return array|null
     */
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'shortDescription' => $data->shortDescription,
            'content' => $data->content,
            'featuredImage' => self::featuredImageUrl($data),
            'locale' => $data->locale,
            'groupId' => $data->groupId,
            'template' => $data->template,
            'status' => $data->status,
            'gallery' => $data->galleryType,
            'createdBy' => optional($data->createdBy)->only('id', 'fullName'),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
            'seo' => SeoParser::first($data->seo),
            'acf' => AcfParser::forContent($data->acf),
        ];
    }

    /**
     * @param $data
     *
     * @return array|null
     */
    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'shortDescription' => $data->shortDescription,
            'featuredImage' => self::featuredImageUrl($data),
            'status' => $data->status,
            'locale' => $data->locale,
            'groupId' => $data->groupId,
            'createdBy' => optional($data->createdBy)->only('id', 'fullName'),
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    private static function featuredImageUrl($data): ?string
    {
        if (!$data->featuredImage) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_PAGE . $data->featuredImage);
    }
}
