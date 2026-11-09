<?php

namespace App\Services\Guesty;

use App\Models\Property\Property;
use App\Models\Property\PropertyVariant;
use App\Services\Constant\Property\PropertyStatus;
use Illuminate\Support\Facades\Log;

class GuestyListingPusher
{
    public function __construct(protected GuestyClient $client) {}

    public function push(Property|PropertyVariant $property): void
    {
        $isParent = $property instanceof Property;

        if (!$this->client->isConfigured() || ($isParent && !$property->guestyId)) {
            return;
        }

        try {
            $payload = $this->payload($property);

            if ($property->guestyId) {
                $this->client->updateListing($property->guestyId, $payload);
                return;
            }

            $listing = $this->client->createListing($payload + ['type' => 'SINGLE']);
            $property->forceFill(['guestyId' => $listing['_id'] ?? null])->saveQuietly();
        } catch (\Throwable $e) {
            Log::warning('Guesty push failed for ' . class_basename($property) . ' #' . $property->id . ': ' . $e->getMessage());
        }
    }

    protected function payload(Property|PropertyVariant $property): array
    {
        $property->loadMissing(['type', 'addresses', 'pricing', 'availability', 'descriptions', 'amenities', 'tags']);

        $description = $property->descriptions->firstWhere('channel', 'primary') ?? $property->descriptions->first();
        $address = $property->addresses->first();
        $pricing = $property->pricing;

        return array_filter([
            'title' => $description?->title ?: $property->nickname,
            'nickname' => $property->nickname,
            'propertyType' => $property->type?->name,
            'roomType' => $property->roomType,
            'accommodates' => $property->occupancy,
            'areaSquareFeet' => $property->propertySize,
            'isListed' => (int) $property->statusId === PropertyStatus::ACTIVE_ID,
            'address' => $address ? array_filter([
                'full' => $address->address,
                'lat' => $address->latitude !== null ? (float) $address->latitude : null,
                'lng' => $address->longitude !== null ? (float) $address->longitude : null,
                'zipcode' => $address->zipCode,
            ]) : null,
            'prices' => array_filter([
                'currency' => $property->currency,
                'basePrice' => $pricing && $pricing->weekdayBasePrice !== null ? (float) $pricing->weekdayBasePrice : null,
                'weekendBasePrice' => $pricing && $pricing->weekendBasePrice !== null ? (float) $pricing->weekendBasePrice : null,
                'cleaningFee' => $pricing && $pricing->cleaningFee !== null ? (float) $pricing->cleaningFee : null,
                'extraPersonFee' => $pricing && $pricing->extraPersonFee !== null ? (float) $pricing->extraPersonFee : null,
                'securityDepositFee' => $pricing && $pricing->securityDepositFee !== null ? (float) $pricing->securityDepositFee : null,
            ], fn ($v) => $v !== null),
            'terms' => array_filter([
                'minNights' => $property->availability?->minLengthOfStay,
                'maxNights' => $property->availability?->maxLengthOfStay,
            ]),
            'publicDescription' => array_filter([
                'summary' => $description?->summary,
                'space' => $description?->theSpace,
                'access' => $description?->guestAccess,
                'neighborhood' => $description?->theNeighborhood,
                'transit' => $description?->gettingAround,
                'notes' => $description?->otherThingsToNote,
            ]),
            'amenities' => $property->amenities->pluck('name')->all(),
            'tags' => $property->tags->pluck('name')->all(),
        ], fn ($v) => $v !== null && $v !== []);
    }
}
