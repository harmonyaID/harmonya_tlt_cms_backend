<?php

namespace App\Parser\Boat;

use Logia\Core\Parser\BaseParser;

class BoatTypeParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        $discount = optional($data->boat)->discountPercentage ?? 0;

        return [
            'id' => $data->id,
            'boatId' => $data->boatId,
            'boat' => optional($data->boat)->only('id', 'name'),
            'name' => $data->name,
            'description' => $data->description,
            'currency' => $data->currency,
            'priceReturnAdult' => $data->priceReturnAdult,
            'priceReturnChild' => $data->priceReturnChild,
            'priceOneWayAdult' => $data->priceOneWayAdult,
            'priceOneWayChild' => $data->priceOneWayChild,
            'discountedReturnAdult' => $data->discountedPrice($data->priceReturnAdult, $discount),
            'discountedReturnChild' => $data->discountedPrice($data->priceReturnChild, $discount),
            'discountedOneWayAdult' => $data->discountedPrice($data->priceOneWayAdult, $discount),
            'discountedOneWayChild' => $data->discountedPrice($data->priceOneWayChild, $discount),
            'childAgeNote' => $data->childAgeNote,
            'isActive' => $data->isActive,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        $discount = optional($data->boat)->discountPercentage ?? 0;

        return [
            'id' => $data->id,
            'boatId' => $data->boatId,
            'name' => $data->name,
            'description' => $data->description,
            'currency' => $data->currency,
            'priceReturnAdult' => $data->priceReturnAdult,
            'priceReturnChild' => $data->priceReturnChild,
            'priceOneWayAdult' => $data->priceOneWayAdult,
            'priceOneWayChild' => $data->priceOneWayChild,
            'discountedReturnAdult' => $data->discountedPrice($data->priceReturnAdult, $discount),
            'discountedReturnChild' => $data->discountedPrice($data->priceReturnChild, $discount),
            'discountedOneWayAdult' => $data->discountedPrice($data->priceOneWayAdult, $discount),
            'discountedOneWayChild' => $data->discountedPrice($data->priceOneWayChild, $discount),
            'childAgeNote' => $data->childAgeNote,
            'isActive' => $data->isActive,
        ];
    }
}