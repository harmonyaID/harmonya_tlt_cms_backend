<?php

namespace App\Services\Guesty;

use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\PropertyAvailability;
use App\Models\Property\PropertyBedType;
use App\Models\Property\PropertyDescription;
use App\Models\Property\PropertyGuestInfo;
use App\Models\Property\PropertyPhoto;
use App\Models\Property\PropertyPricing;
use App\Models\Property\PropertyRoom;
use App\Models\Property\PropertyRoomType;
use App\Models\Property\PropertyTag;
use App\Models\Property\PropertyType;
use App\Models\Setting\SettingAmenity;
use App\Services\Constant\Property\PropertyAddressType;
use App\Services\Constant\Property\PropertyAdvanceNoticeUnit;
use App\Services\Constant\Property\PropertyAvailabilityType;
use App\Services\Constant\Property\PropertyCleaningFeeType;
use App\Services\Constant\Property\PropertyCleaningStatus;
use App\Services\Constant\Property\PropertyListingType;
use App\Services\Constant\Property\PropertySourceType;
use App\Services\Constant\Property\PropertyStatus;
use App\Services\Constant\Property\PropertyUnitType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\DB;

class GuestyPropertyImporter
{
    public function __construct(protected GuestyClient $client)
    {
    }

    public function import(array $listing): Property
    {
        return DB::transaction(function () use ($listing) {

            $property = Property::updateOrCreate(
                ['guestyId' => $listing['_id']],
                [
                    'nickname' => $listing['nickname'] ?? $listing['title'] ?? 'Untitled',
                    'propertyTypeId' => $this->resolvePropertyTypeId($listing['propertyType'] ?? null),
                    'unitTypeId' => $this->mapUnitType($listing['type'] ?? 'SINGLE'),
                    'listingTypeId' => $this->mapListingType($listing['roomType'] ?? null),
                    'roomType' => $listing['roomType'] ?? null,
                    'occupancy' => $listing['accommodates'] ?? null,
                    'propertySize' => $listing['areaSquareFeet'] ?? null,
                    'statusId' => ($listing['isListed'] ?? false) ? PropertyStatus::ACTIVE_ID : PropertyStatus::INACTIVE_ID,
                    'cleaningStatusId' => $this->mapCleaningStatus($listing['cleaningStatus']['value'] ?? null),
                    'sourceTypeId' => PropertySourceType::GUESTY_ID,
                    'currency' => $listing['prices']['currency'] ?? 'USD',
                    'guestyImportedAt' => now(),
                ]
            );

            $this->syncAddress($property, $listing);
            $this->syncGuestInfo($property, $listing);
            $this->syncAvailability($property, $listing);
            $this->syncPricing($property, $listing);
            $this->syncDescriptions($property, $listing);
            $this->syncRooms($property, $listing);
            $this->syncAmenities($property, $listing);
            $this->syncTags($property, $listing);
            $this->syncPhotos($property, $listing);

            return $property;
        });
    }

    private function syncAddress(Property $property, array $listing): void
    {
        $address = $listing['address'] ?? null;
        if (!$address) {
            return;
        }

        PropertyAddress::updateOrCreate(
            ['propertyId' => $property->id, 'typeId' => PropertyAddressType::FULL_ID],
            [
                'address' => $address['full'] ?? null,
                'buildingName' => $address['apt'] ?? null,
                'latitude' => $address['lat'] ?? null,
                'longitude' => $address['lng'] ?? null,
                'zipCode' => $address['zipcode'] ?? null,
            ]
        );
    }

    private function syncGuestInfo(Property $property, array $listing): void
    {
        $description = $listing['publicDescription'] ?? [];

        PropertyGuestInfo::updateOrCreate(
            ['propertyId' => $property->id],
            [
                'hostName' => $listing['hostName'] ?? null,
                'wifiName' => $listing['wifiName'] ?? null,
                'wifiPassword' => $listing['wifiPassword'] ?? null,
                'houseManual' => $listing['houseManual'] ?? null,
                'trashInstructions' => $listing['trashCollectedOn'] ?? null,
                'parkingInstructions' => $listing['parkingInstructions'] ?? null,
                'cleaningInstructions' => null,
                'interactionWithGuests' => $description['interactionWithGuests'] ?? null,
            ]
        );
    }

    private function syncAvailability(Property $property, array $listing): void
    {
        $terms = $listing['terms'] ?? [];
        $calendarRules = $listing['calendarRules'] ?? [];
        $advanceNotice = $calendarRules['advanceNotice']['defaultSettings']['hours'] ?? null;

        PropertyAvailability::updateOrCreate(
            ['propertyId' => $property->id],
            [
                'defaultAvailabilityId' => ($calendarRules['defaultAvailability'] ?? null) === 'AVAILABLE'
                    ? PropertyAvailabilityType::ALWAYS_ID
                    : PropertyAvailabilityType::CUSTOM_ID,
                'advanceNoticeValue' => $advanceNotice,
                'advanceNoticeUnitId' => $advanceNotice !== null ? PropertyAdvanceNoticeUnit::HOUR_ID : null,
                'minLengthOfStay' => $terms['minNights'] ?? null,
                'maxLengthOfStay' => $terms['maxNights'] ?? null,
            ]
        );
    }

    private function syncPricing(Property $property, array $listing): void
    {
        $prices = $listing['prices'] ?? [];
        $cleaningFeeValue = $listing['financials']['cleaningFee']['value'] ?? null;

        PropertyPricing::updateOrCreate(
            ['propertyId' => $property->id],
            [
                'weekdayBasePrice' => $prices['basePrice'] ?? null,
                'weekendBasePrice' => $prices['weekendBasePrice'] ?? null,
                'rateStrategy' => $listing['yieldManagement']['rateStrategy']['name'] ?? null,
                'cleaningFee' => $prices['cleaningFee'] ?? ($cleaningFeeValue['formula'] ?? null),
                'cleaningFeeTypeId' => $this->mapCleaningFeeType($cleaningFeeValue['multiplier'] ?? null),
                'extraPersonFee' => $prices['extraPersonFee'] ?? null,
                'securityDepositFee' => $prices['securityDepositFee'] ?? null,
                'weeklyDiscount' => isset($prices['weeklyPriceFactor']) ? round((1 - $prices['weeklyPriceFactor']) * 100, 2) : 0,
                'monthlyDiscount' => isset($prices['monthlyPriceFactor']) ? round((1 - $prices['monthlyPriceFactor']) * 100, 2) : 0,
            ]
        );
    }

    private function syncDescriptions(Property $property, array $listing): void
    {
        $description = $listing['publicDescription'] ?? [];

        PropertyDescription::updateOrCreate(
            ['propertyId' => $property->id, 'channel' => 'primary', 'language' => 'en'],
            [
                'title' => $listing['title'] ?? null,
                'summary' => $description['summary'] ?? null,
                'theSpace' => $description['space'] ?? null,
                'guestAccess' => $description['access'] ?? null,
                'theNeighborhood' => $description['neighborhood'] ?? null,
                'gettingAround' => $description['transit'] ?? null,
                'otherThingsToNote' => $description['notes'] ?? null,
            ]
        );
    }

    private function syncRooms(Property $property, array $listing): void
    {
        $listingRooms = $listing['listingRooms'] ?? [];
        if (empty($listingRooms)) {
            return;
        }

        $bedroomType = PropertyRoomType::firstOrCreate(['name' => 'Bedroom']);

        $property->rooms()->delete();

        foreach ($listingRooms as $room) {
            $beds = $room['beds'] ?? [];
            if (empty($beds)) {
                continue;
            }

            foreach ($beds as $bed) {
                $bedType = PropertyBedType::firstOrCreate(['name' => $this->mapBedTypeName($bed['type'] ?? null)]);

                PropertyRoom::create([
                    'propertyId' => $property->id,
                    'roomTypeId' => $bedroomType->id,
                    'label' => 'Bedroom ' . (($room['roomNumber'] ?? 0) + 1),
                    'bedTypeId' => $bedType->id,
                    'bedCount' => $bed['quantity'] ?? 1,
                    'order' => $room['roomNumber'] ?? 0,
                ]);
            }
        }
    }

    private function syncAmenities(Property $property, array $listing): void
    {
        $names = $listing['amenities'] ?? [];
        if (empty($names)) {
            return;
        }

        $ids = collect($names)->unique()->map(function ($name) {
            return SettingAmenity::firstOrCreate(['name' => $name])->id;
        });

        $property->amenities()->sync($ids);
    }

    private function syncTags(Property $property, array $listing): void
    {
        $names = $listing['tags'] ?? [];
        if (empty($names)) {
            return;
        }

        $ids = collect($names)->unique()->map(function ($name) {
            return PropertyTag::firstOrCreate(['name' => $name])->id;
        });

        $property->tags()->sync($ids);
    }

    private function syncPhotos(Property $property, array $listing): void
    {
        if ($property->photos()->exists()) {
            return;
        }

        $pictures = $listing['pictures'] ?? [];
        if (empty($pictures)) {
            return;
        }

        $dirPath = PathConstant::IMAGES_PROPERTY_PHOTO_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        foreach ($pictures as $index => $picture) {
            $url = $picture['original'] ?? null;
            if (!$url) {
                continue;
            }

            $bytes = $this->client->downloadImage($url);
            if (!$bytes) {
                continue;
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $property->guestyId . '-' . $index . '.' . $extension;
            file_put_contents($dirPath . $filename, $bytes);

            PropertyPhoto::create([
                'propertyId' => $property->id,
                'path' => $filename,
                'caption' => $picture['caption'] ?? null,
                'order' => $index,
            ]);
        }
    }

    private function resolvePropertyTypeId(?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        return PropertyType::firstOrCreate(['name' => $name])->id;
    }

    private function mapUnitType(string $type): int
    {
        return match ($type) {
            'MTL', 'MTL_CHILD' => PropertyUnitType::MULTI_UNIT_ID,
            default => PropertyUnitType::SINGLE_UNIT_ID,
        };
    }

    private function mapListingType(?string $roomType): int
    {
        return match ($roomType) {
            'Private room' => PropertyListingType::PRIVATE_ROOM_ID,
            'Shared room' => PropertyListingType::SHARED_ROOM_ID,
            default => PropertyListingType::ENTIRE_HOME_ID,
        };
    }

    private function mapCleaningStatus(?string $status): int
    {
        return match ($status) {
            'dirty' => PropertyCleaningStatus::DIRTY_ID,
            'clean' => PropertyCleaningStatus::CLEAN_ID,
            'waiting_for_inspection', 'inspection' => PropertyCleaningStatus::WAITING_FOR_INSPECTION_ID,
            null => PropertyCleaningStatus::NOT_SET_ID,
            default => PropertyCleaningStatus::UNKNOWN_ID,
        };
    }

    private function mapCleaningFeeType(?string $multiplier): ?int
    {
        return match ($multiplier) {
            'PER_NIGHT' => PropertyCleaningFeeType::PER_NIGHT_ID,
            'PER_STAY' => PropertyCleaningFeeType::PER_STAY_ID,
            default => null,
        };
    }

    private function mapBedTypeName(?string $type): string
    {
        if (!$type) {
            return 'Bed';
        }

        return ucwords(strtolower(str_replace('_', ' ', $type)));
    }
}