<?php

namespace App\Services\Guesty;

use App\Models\Property\Property;
use App\Models\Property\PropertyVariant;
use App\Services\Constant\Property\PropertyAddressType;
use App\Services\Constant\Property\PropertyGuestySyncStatus;
use App\Services\Constant\Property\PropertyListingType;
use App\Services\Constant\Property\PropertyStatus;
use Illuminate\Support\Facades\Log;

class GuestyListingPusher
{
    public function __construct(protected GuestyClient $client) {}

    public function push(Property|PropertyVariant $property): void
    {
        if (!$this->shouldPush($property)) {
            return;
        }

        try {
            $payload = $this->payload($property);

            if ($property->guestyId) {
                $listing = $this->client->updateListing($property->guestyId, $payload);
            } else {
                $listing = $this->client->createListing($payload);
                $property->guestyId = $listing['_id'] ?? null;
            }

            $property->forceFill([
                'guestySyncStatusId' => PropertyGuestySyncStatus::SYNCED_ID,
                'guestySyncedAt' => now(),
                'guestySyncError' => null,
            ])->saveQuietly();
        } catch (\Throwable $e) {
            Log::warning('Guesty push failed for ' . class_basename($property) . ' #' . $property->id . ': ' . $e->getMessage());

            $property->forceFill([
                'guestySyncStatusId' => PropertyGuestySyncStatus::FAILED_ID,
                'guestySyncError' => mb_substr($e->getMessage(), 0, 2000),
            ])->saveQuietly();
        }
    }

    protected function shouldPush(Property|PropertyVariant $property): bool
    {
        if (!config('guesty.push_enabled') || !$this->client->isConfigured()) {
            return false;
        }

        return $property instanceof PropertyVariant || (bool) $property->guestyId;
    }

    protected function payload(Property|PropertyVariant $property): array
    {
        $property->loadMissing([
            'type', 'addresses', 'guestInfo', 'rooms.bedType', 'availability', 'pricing',
            'descriptions', 'amenities', 'tags', 'features',
        ]);

        $description = $property->descriptions->firstWhere('channel', 'primary')
            ?? $property->descriptions->first();

        $payload = [
            'title' => $description?->title ?: $property->nickname,
            'nickname' => $property->nickname,
            'roomType' => $property->roomType ?: $this->roomType($property->listingTypeId),
            'accommodates' => $property->occupancy,
            'areaSquareFeet' => $property->propertySize,
            'isListed' => (int) $property->statusId === PropertyStatus::ACTIVE_ID,
            'prices' => ['currency' => $property->currency ?: 'USD'],
        ];

        if (!$property->guestyId) {
            $payload['type'] = 'SINGLE';
        }

        if ($property->type?->name) {
            $payload['propertyType'] = $property->type->name;
        }

        if ($address = $this->address($property)) {
            $payload['address'] = $address;
        }

        if ($pricing = $property->pricing) {
            $payload['prices'] += array_filter([
                'basePrice' => $this->number($pricing->weekdayBasePrice),
                'weekendBasePrice' => $this->number($pricing->weekendBasePrice),
                'cleaningFee' => $this->number($pricing->cleaningFee),
                'extraPersonFee' => $this->number($pricing->extraPersonFee),
                'securityDepositFee' => $this->number($pricing->securityDepositFee),
            ], fn ($v) => $v !== null);

            $payload['prices']['weeklyPriceFactor'] = round(1 - ((float) $pricing->weeklyDiscount) / 100, 4);
            $payload['prices']['monthlyPriceFactor'] = round(1 - ((float) $pricing->monthlyDiscount) / 100, 4);
        }

        if ($availability = $property->availability) {
            $terms = array_filter([
                'minNights' => $availability->minLengthOfStay,
                'maxNights' => $availability->maxLengthOfStay,
            ], fn ($v) => $v !== null);

            if ($terms) {
                $payload['terms'] = $terms;
            }
        }

        if ($description) {
            $public = array_filter([
                'summary' => $description->summary,
                'space' => $description->theSpace,
                'access' => $description->guestAccess,
                'neighborhood' => $description->theNeighborhood,
                'transit' => $description->gettingAround,
                'notes' => $description->otherThingsToNote,
                'interactionWithGuests' => $property->guestInfo?->interactionWithGuests,
            ], fn ($v) => $v !== null && $v !== '');

            if ($public) {
                $payload['publicDescription'] = $public;
            }
        }

        if ($info = $property->guestInfo) {
            $payload += array_filter([
                'wifiName' => $info->wifiName,
                'wifiPassword' => $info->wifiPassword,
                'houseManual' => $info->houseManual,
            ], fn ($v) => $v !== null && $v !== '');
        }

        if ($property->amenities->isNotEmpty()) {
            $payload['amenities'] = $property->amenities->pluck('name')->values()->all();
        }

        if ($property->tags->isNotEmpty()) {
            $payload['tags'] = $property->tags->pluck('name')->values()->all();
        }

        foreach ($property->features as $feature) {
            $key = ['Bedroom' => 'bedrooms', 'Bathroom' => 'bathrooms'][$feature->name] ?? null;

            if ($key && $feature->pivot?->value !== null) {
                $payload[$key] = (float) $feature->pivot->value;
            }
        }

        if ($rooms = $this->rooms($property)) {
            $payload['listingRooms'] = $rooms;
        }

        return array_filter($payload, fn ($v) => $v !== null);
    }

    protected function address(Property|PropertyVariant $property): ?array
    {
        $address = $property->addresses->firstWhere('typeId', PropertyAddressType::FULL_ID)
            ?? $property->addresses->first();

        if (!$address) {
            return null;
        }

        return array_filter([
            'full' => $address->address,
            'apt' => $address->buildingName,
            'lat' => $this->number($address->latitude),
            'lng' => $this->number($address->longitude),
            'zipcode' => $address->zipCode,
        ], fn ($v) => $v !== null && $v !== '');
    }

    protected function rooms(Property|PropertyVariant $property): array
    {
        $rooms = [];

        foreach ($property->rooms->groupBy('order') as $order => $group) {
            $beds = $group->filter(fn ($r) => $r->bedType)->map(fn ($r) => [
                'type' => strtoupper(str_replace(' ', '_', trim($r->bedType->name))),
                'quantity' => (int) ($r->bedCount ?: 1),
            ])->values()->all();

            if ($beds) {
                $rooms[] = ['roomNumber' => (int) $order, 'beds' => $beds];
            }
        }

        return $rooms;
    }

    protected function roomType(?int $listingTypeId): string
    {
        return PropertyListingType::OPTION[$listingTypeId] ?? PropertyListingType::ENTIRE_HOME;
    }

    protected function number(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }
}
