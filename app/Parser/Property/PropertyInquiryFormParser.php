<?php

namespace App\Parser\Property;

use App\Services\Constant\Global\MailStatus;
use Logia\Core\Parser\BaseParser;

class PropertyInquiryFormParser extends BaseParser
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
            'countryCode' => $data->countryCode,
            'mobileNumber' => $data->mobileNumber,
            'checkInDate' => optional($data->checkInDate)->format('d/m/Y'),
            'checkOutDate' => optional($data->checkOutDate)->format('d/m/Y'),
            'isDatesFlexible' => $data->isDatesFlexible,
            'flexibleMonth' => $data->flexibleMonth,
            'flexibleYear' => $data->flexibleYear,
            'adultCount' => $data->adultCount,
            'childrenAges' => $data->childrenAges,
            'childCount' => $data->childCount(),
            'comments' => $data->comments,
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
            'countryCode' => $data->countryCode,
            'mobileNumber' => $data->mobileNumber,
            'checkInDate' => optional($data->checkInDate)->format('d/m/Y'),
            'checkOutDate' => optional($data->checkOutDate)->format('d/m/Y'),
            'isDatesFlexible' => $data->isDatesFlexible,
            'adultCount' => $data->adultCount,
            'childCount' => $data->childCount(),
            'isRead' => $data->isRead,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
