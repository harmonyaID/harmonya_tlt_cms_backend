<?php

namespace App\Http\Requests\Property;

use App\Http\Requests\Seo\SeoRule;
use Logia\Core\Validation\Support\FormRequest;

class PropertyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return array_merge([
            'nickname' => 'required|string',
            'propertyTypeId' => 'nullable|integer|exists:property_types,id',
            'unitTypeId' => 'required|integer',
            'listingTypeId' => 'required|integer',
            'roomType' => 'nullable|string',
            'occupancy' => 'nullable|integer|min:0',
            'propertySize' => 'nullable|numeric|min:0',
            'statusId' => 'required|integer',
            'cleaningStatusId' => 'nullable|integer',
            'currency' => 'nullable|string|max:10',

            'addresses' => 'nullable|array',
            'addresses.*.typeId' => 'required|integer',
            'addresses.*.address' => 'nullable|string',
            'addresses.*.buildingName' => 'nullable|string',
            'addresses.*.latitude' => 'nullable|numeric',
            'addresses.*.longitude' => 'nullable|numeric',
            'addresses.*.zipCode' => 'nullable|string',

            'guestInfo' => 'nullable|array',
            'guestInfo.hostName' => 'nullable|string',
            'guestInfo.wifiName' => 'nullable|string',
            'guestInfo.wifiPassword' => 'nullable|string',
            'guestInfo.houseManual' => 'nullable|string',
            'guestInfo.trashInstructions' => 'nullable|string',
            'guestInfo.parkingInstructions' => 'nullable|string',
            'guestInfo.cleaningInstructions' => 'nullable|string',
            'guestInfo.interactionWithGuests' => 'nullable|string',

            'rooms' => 'nullable|array',
            'rooms.*.roomTypeId' => 'required|integer|exists:property_room_types,id',
            'rooms.*.label' => 'nullable|string',
            'rooms.*.bedTypeId' => 'nullable|integer|exists:property_bed_types,id',
            'rooms.*.bedCount' => 'nullable|integer|min:0',
            'rooms.*.order' => 'nullable|integer',

            'availability' => 'nullable|array',
            'availability.defaultAvailabilityId' => 'nullable|integer',
            'availability.bookingWindow' => 'nullable|string',
            'availability.advanceNoticeValue' => 'nullable|integer|min:0',
            'availability.advanceNoticeUnitId' => 'nullable|integer',
            'availability.preparationTimeValue' => 'nullable|integer|min:0',
            'availability.checkInRestrictions' => 'nullable|string',
            'availability.maxNightsPerYear' => 'nullable|integer|min:0',
            'availability.minLengthOfStay' => 'nullable|integer|min:0',
            'availability.maxLengthOfStay' => 'nullable|integer|min:0',

            'pricing' => 'nullable|array',
            'pricing.weekdayBasePrice' => 'nullable|numeric|min:0',
            'pricing.weekendBasePrice' => 'nullable|numeric|min:0',
            'pricing.rateStrategy' => 'nullable|string',
            'pricing.cleaningFee' => 'nullable|numeric|min:0',
            'pricing.cleaningFeeTypeId' => 'nullable|integer',
            'pricing.extraPersonFee' => 'nullable|numeric|min:0',
            'pricing.securityDepositFee' => 'nullable|numeric|min:0',
            'pricing.weeklyDiscount' => 'nullable|numeric|min:0|max:100',
            'pricing.monthlyDiscount' => 'nullable|numeric|min:0|max:100',
            'pricing.markupPercent' => 'nullable|numeric',

            'descriptions' => 'nullable|array',
            'descriptions.*.channel' => 'nullable|string',
            'descriptions.*.language' => 'nullable|string',
            'descriptions.*.title' => 'nullable|string',
            'descriptions.*.summary' => 'nullable|string',
            'descriptions.*.theSpace' => 'nullable|string',
            'descriptions.*.guestAccess' => 'nullable|string',
            'descriptions.*.theNeighborhood' => 'nullable|string',
            'descriptions.*.gettingAround' => 'nullable|string',
            'descriptions.*.otherThingsToNote' => 'nullable|string',

            'amenityIds' => 'nullable|array',
            'amenityIds.*' => 'integer|exists:setting_amenities,id',

            'tagIds' => 'nullable|array',
            'tagIds.*' => 'integer|exists:property_tags,id',

            'seo' => 'nullable|array',
        ], SeoRule::rules());
    }
}