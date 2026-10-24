<?php

namespace App\Parser\Boat;

use App\Models\Boat\BoatContactForm;
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
        $boatParser = BoatParser::brief($data->boat);

        return [
            'id' => $data->id,
            'boat' => $boatParser,
            'name' => $data->name,
            'status' => $status,
            'email' => $data->email,
            'phone' => $data->phone,
            'ticketType' => $data->ticketType,
            'fromLocation' => $data->fromLocation,
            'toLocation' => $data->toLocation,
            'departureDate' => optional($data->departureDate)->format('d/m/Y'),
            'departureTime' => $data->departureTime,
            'returnDate' => optional($data->returnDate)->format('d/m/Y'),
            'returnTime' => $data->returnTime,
            'adultCount' => $data->adultCount,
            'childCount' => $data->childCount,
            'infantCount' => $data->infantCount,
            'phoneCountryCode' => $data->phoneCountryCode,
            'needLandTransport' => $data->needLandTransport,
            'departurePickupPoint' => $data->departurePickupPoint,
            'departurePickupPointLabel' => BoatContactForm::pointLabel($data->departurePickupPoint),
            'departurePickupFlightNumber' => $data->departurePickupFlightNumber,
            'departurePickupArrivalTime' => $data->departurePickupArrivalTime,
            'departureDropOffPoint' => $data->departureDropOffPoint,
            'departureDropOffPointLabel' => BoatContactForm::pointLabel($data->departureDropOffPoint),
            'departureDropOffLocation' => $data->departureDropOffLocation,
            'departureDropOffArrivalTime' => $data->departureDropOffArrivalTime,
            'returnPickupPoint' => $data->returnPickupPoint,
            'returnPickupPointLabel' => BoatContactForm::pointLabel($data->returnPickupPoint),
            'returnPickupLocation' => $data->returnPickupLocation,
            'returnPickupCheckoutTime' => $data->returnPickupCheckoutTime,
            'returnDropOffPoint' => $data->returnDropOffPoint,
            'returnDropOffPointLabel' => BoatContactForm::pointLabel($data->returnDropOffPoint),
            'returnDropOffLocation' => $data->returnDropOffLocation,
            'bookedThroughTlt' => $data->bookedThroughTlt,
            'tltBookingRefName' => $data->tltBookingRefName,
            'hasSurfboard' => $data->hasSurfboard,
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

        $boatParser = BoatParser::brief($data->boat);

        return [
            'id' => $data->id,
            'boat' => $boatParser,
            'name' => $data->name,
            'status' => $status,
            'email' => $data->email,
            'phone' => $data->phone,
            'ticketType' => $data->ticketType,
            'adultCount' => $data->adultCount,
            'childCount' => $data->childCount,
            'infantCount' => $data->infantCount,
            'fromLocation' => $data->fromLocation,
            'toLocation' => $data->toLocation,
            'departureDate' => optional($data->departureDate)->format('d/m/Y'),
            'returnDate' => optional($data->returnDate)->format('d/m/Y'),
            'isRead' => $data->isRead,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
