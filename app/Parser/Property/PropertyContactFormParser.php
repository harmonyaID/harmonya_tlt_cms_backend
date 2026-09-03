<?php

namespace App\Parser\Property;

use App\Services\Constant\Global\MailStatus;
use Logia\Core\Parser\BaseParser;

class PropertyContactFormParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'status' => MailStatus::idName($data->statusId),
            'property' => optional($data->property)->only('id', 'nickname'),
            'sourceType' => optional($data->sourceType)->only('id', 'name'),
            'name' => $data->name,
            'email' => $data->email,
            'subject' => $data->subject,
            'phone' => $data->phone,
            'message' => $data->message,
            'isRead' => $data->isRead,
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
            'status' => MailStatus::idName($data->statusId),
            'property' => optional($data->property)->only('id', 'nickname'),
            'sourceType' => optional($data->sourceType)->only('id', 'name'),
            'name' => $data->name,
            'email' => $data->email,
            'subject' => $data->subject,
            'phone' => $data->phone,
            'isRead' => $data->isRead,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
