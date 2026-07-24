<?php

namespace App\Parser\Property;

use App\Services\Constant\Global\MailStatus;
use App\Services\Constant\Property\PropertySourceType;
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
            'sourceType' => PropertySourceType::idName($data->sourceTypeId),
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'checkInDate' => optional($data->checkInDate)->format('d/m/Y'),
            'checkOutDate' => optional($data->checkOutDate)->format('d/m/Y'),
            'adultCount' => $data->adultCount,
            'childCount' => $data->childCount,
            'infantCount' => $data->infantCount,
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
            'sourceType' => PropertySourceType::idName($data->sourceTypeId),
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'checkInDate' => optional($data->checkInDate)->format('d/m/Y'),
            'checkOutDate' => optional($data->checkOutDate)->format('d/m/Y'),
            'isRead' => $data->isRead,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
