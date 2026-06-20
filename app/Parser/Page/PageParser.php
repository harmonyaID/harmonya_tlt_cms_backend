<?php

namespace App\Parser\Page;

use App\Parser\Staff\StaffParser;
use App\Services\Constant\Setting\Gender;
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

        $photos = [];
        foreach ($data->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'photo' => $photo->photoUrl(),
                'url' => $photo->photoUrl(),
            ];
        }

        $acf = [];
        foreach ($data->acf as $key => $acfValue) {
            $acf[$key] = unserialize($acfValue->value);
        }

        return [
            'id' => $data->id,
            'title' => $data->title,
            'shortDescription' => $data->shortDescription,
            'content' => $data->content,
            'featuredImage' => $data->featuredImage,
            'locale' => $data->locale,
            'groupId' => $data->groupId,
            'template' => $data->template,
            'createdBy' => StaffParser::brief($data->createdBy),
            'createdAt' => optional($data->created_at)->format('M d, Y h:i a'),
            'seo' => $data->seo,
            'seoImage' => $data->seoImage,
            'status' => $data->status,
            'gallery' => $data->galleryType,
            'photos' => $photos,
            'acf' => $acf,
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
            'description' => $data->description,
            'createdBy' => StaffParser::brief($data->createdBy),
            'createdAt' => optional($data->created_at)->format('d/m/Y H:i'),
        ];
    }

}
