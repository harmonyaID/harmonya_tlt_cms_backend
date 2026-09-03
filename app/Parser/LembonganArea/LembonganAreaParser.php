<?php

namespace App\Parser\LembonganArea;

use Logia\Core\Parser\BaseParser;

class LembonganAreaParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'content' => $data->content,
            'featuredImage' => $data->featuredImageUrl(),
            'url' => $data->url,
            'order' => $data->order,
            'isActive' => $data->isActive,
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
            'name' => $data->name,
            'description' => $data->description,
            'featuredImage' => $data->featuredImageUrl(),
            'url' => $data->url,
            'order' => $data->order,
            'isActive' => $data->isActive,
        ];
    }
}
