<?php

namespace App\Parser\Experience;

use Logia\Core\Parser\BaseParser;

class ExperienceCategoryParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        return [
            'id' => $data->id,
            'type' => optional($data->type)->only('id', 'name'),
            'name' => $data->name,
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
        ];
    }
}