<?php

namespace App\Parser\Property;

use App\Parser\Seo\SeoParser;
use App\Services\Constant\Property\PropertyAdvanceNoticeUnit;
use App\Services\Constant\Property\PropertyAvailabilityType;
use App\Services\Constant\Property\PropertyCleaningFeeType;
use App\Services\Constant\Property\PropertyCleaningStatus;
use App\Services\Constant\Property\PropertyListingType;
use App\Services\Constant\Property\PropertySourceType;
use App\Services\Constant\Property\PropertyStatus;
use App\Services\Constant\Property\PropertyUnitType;
use App\Services\Constant\Property\PropertyAddressType;
use Logia\Core\Parser\BaseParser;

class PropertyParser extends BaseParser
{

    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'nickname' => $data->nickname,
            'type' => optional($data->type)->only('id', 'name'),
            'unitType' => PropertyUnitType::idName($data->unitTypeId),
            'listingType' => PropertyListingType::idName($data->listingTypeId),
            'roomType' => $data->roomType,
            'occupancy' => $data->occupancy,
            'propertySize' => $data->propertySize,
            'status' => PropertyStatus::idName($data->statusId),
            'cleaningStatus' => PropertyCleaningStatus::idName($data->cleaningStatusId),
            'sourceType' => PropertySourceType::idName($data->sourceTypeId),
            'currency' => $data->currency,

            'addresses' => $data->addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'type' => PropertyAddressType::idName($address->typeId),
                    'address' => $address->address,
                    'buildingName' => $address->buildingName,
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                    'zipCode' => $address->zipCode,
                ];
            }),

            'guestInfo' => optional($data->guestInfo)->only([
                'hostName', 'wifiName', 'wifiPassword', 'houseManual',
                'trashInstructions', 'parkingInstructions', 'cleaningInstructions', 'interactionWithGuests',
            ]),

            'rooms' => $data->rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'roomType' => optional($room->roomType)->only('id', 'name'),
                    'label' => $room->label,
                    'bedType' => optional($room->bedType)->only('id', 'name'),
                    'bedCount' => $room->bedCount,
                    'order' => $room->order,
                ];
            }),

            'availability' => $data->availability ? [
                'defaultAvailability' => PropertyAvailabilityType::idName($data->availability->defaultAvailabilityId),
                'bookingWindow' => $data->availability->bookingWindow,
                'advanceNoticeValue' => $data->availability->advanceNoticeValue,
                'advanceNoticeUnit' => PropertyAdvanceNoticeUnit::idName($data->availability->advanceNoticeUnitId),
                'preparationTimeValue' => $data->availability->preparationTimeValue,
                'checkInRestrictions' => $data->availability->checkInRestrictions,
                'maxNightsPerYear' => $data->availability->maxNightsPerYear,
                'minLengthOfStay' => $data->availability->minLengthOfStay,
                'maxLengthOfStay' => $data->availability->maxLengthOfStay,
            ] : null,

            'pricing' => $data->pricing ? [
                'weekdayBasePrice' => $data->pricing->weekdayBasePrice,
                'weekendBasePrice' => $data->pricing->weekendBasePrice,
                'rateStrategy' => $data->pricing->rateStrategy,
                'cleaningFee' => $data->pricing->cleaningFee,
                'cleaningFeeType' => PropertyCleaningFeeType::idName($data->pricing->cleaningFeeTypeId),
                'extraPersonFee' => $data->pricing->extraPersonFee,
                'securityDepositFee' => $data->pricing->securityDepositFee,
                'weeklyDiscount' => $data->pricing->weeklyDiscount,
                'monthlyDiscount' => $data->pricing->monthlyDiscount,
                'markupPercent' => $data->pricing->markupPercent,
            ] : null,

            'descriptions' => $data->descriptions->map(function ($description) {
                return [
                    'id' => $description->id,
                    'channel' => $description->channel,
                    'language' => $description->language,
                    'title' => $description->title,
                    'summary' => $description->summary,
                    'theSpace' => $description->theSpace,
                    'guestAccess' => $description->guestAccess,
                    'theNeighborhood' => $description->theNeighborhood,
                    'gettingAround' => $description->gettingAround,
                    'otherThingsToNote' => $description->otherThingsToNote,
                ];
            }),

            'photos' => $data->photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'url' => $photo->pathUrl(),
                    'caption' => $photo->caption,
                    'order' => $photo->order,
                ];
            }),

            'amenities' => $data->amenities->map(function ($amenity) {
                return ['id' => $amenity->id, 'name' => $amenity->name];
            }),

            'tags' => $data->tags->map(function ($tag) {
                return ['id' => $tag->id, 'name' => $tag->name];
            }),

            'seo' => SeoParser::first($data->seo),

            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        $cover = $data->photos->first();

        return [
            'id' => $data->id,
            'nickname' => $data->nickname,
            'type' => optional($data->type)->only('id', 'name'),
            'unitType' => PropertyUnitType::idName($data->unitTypeId),
            'occupancy' => $data->occupancy,
            'status' => PropertyStatus::idName($data->statusId),
            'cleaningStatus' => PropertyCleaningStatus::idName($data->cleaningStatusId),
            'sourceType' => PropertySourceType::idName($data->sourceTypeId),
            'coverPhoto' => $cover ? $cover->pathUrl() : null,
            'address' => optional($data->addresses->first())->address,
        ];
    }
}