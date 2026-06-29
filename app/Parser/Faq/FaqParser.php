<?php

namespace App\Parser\Faq;

use Logia\Core\Parser\BaseParser;

class FaqParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        return [
            'id'        => $data->id,
            'question'  => $data->question,
            'answer'    => $data->answer,
            'order'     => $data->order,
            'isActive'  => $data->isActive,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        return [
            'id'        => $data->id,
            'question'  => $data->question,
            'answer'    => $data->answer,
            'order'     => $data->order,
            'isActive'  => $data->isActive,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}