<?php

namespace App\Parser\Boat;

use App\Services\Constant\Global\MailStatus;
use Logia\Core\Parser\BaseParser;

class BoatContactFormParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        $status = MailStatus::idName($data->statusId);

        return [
            'id' => $data->id,
            'boat' => optional($data->boat)->only('id', 'name', 'routeFrom', 'routeTo'),
            'boatType' => optional($data->boatType)->only('id', 'name', 'currency'),
            'name' => $data->name,
            'status' => $status,
            'email' => $data->email,
            'phone' => $data->phone,
            'ticketType' => $data->ticketType,
            'baliLandLocation' => $data->baliLandLocation,
            'bookedThroughTlt' => $data->bookedThroughTlt,
            'tltBookingRefName' => $data->tltBookingRefName,
            'adultCount' => $data->adultCount,
            'childCount' => $data->childCount,
            'infantCount' => $data->infantCount,
            'departureDateFromBali' => optional($data->departureDateFromBali)->format('d/m/Y'),
            'departureTimeFromBali' => $data->departureTimeFromBali,
            'pickUpLocationBali' => $data->pickUpLocationBali,
            'flightNumber' => $data->flightNumber,
            'arrivalTime' => $data->arrivalTime,
            'hotelNameBali' => $data->hotelNameBali,
            'hotelContactBali' => $data->hotelContactBali,
            'departureDateFromLembongan' => optional($data->departureDateFromLembongan)->format('d/m/Y'),
            'departureTimeFromLembongan' => $data->departureTimeFromLembongan,
            'dropOffLocationBali' => $data->dropOffLocationBali,
            'flightTime' => $data->flightTime,
            'hotelNameLembongan' => $data->hotelNameLembongan,
            'accommodationLembongan' => $data->accommodationLembongan,
            'passengerNames' => $data->passengerNames,
            'hasSurfboard' => $data->hasSurfboard,
            'hearAboutUs' => $data->hearAboutUs,
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

        $status = MailStatus::idName($data->statusId);

        return [
            'id' => $data->id,
            'boat' => optional($data->boat)->only('id', 'name'),
            'boatType' => optional($data->boatType)->only('id', 'name'),
            'name' => $data->name,
            'status' => $status,
            'email' => $data->email,
            'phone' => $data->phone,
            'ticketType' => $data->ticketType,
            'adultCount' => $data->adultCount,
            'childCount' => $data->childCount,
            'infantCount' => $data->infantCount,
            'departureDateFromBali' => optional($data->departureDateFromBali)->format('d/m/Y'),
            'departureDateFromLembongan' => optional($data->departureDateFromLembongan)->format('d/m/Y'),
            'isRead' => $data->isRead,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
