<?php

namespace App\Parser\Redirection;

use Logia\Core\Parser\BaseParser;

class RedirectionParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'sourceUrl' => $data->sourceUrl,
            'targetUrl' => $data->targetUrl,
            'statusCode' => $data->statusCode,
            'isActive' => $data->isActive,
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
            'name' => $data->name,
            'sourceUrl' => $data->sourceUrl,
            'targetUrl' => $data->targetUrl,
            'statusCode' => $data->statusCode,
            'isActive' => $data->isActive,
        ];
    }
}