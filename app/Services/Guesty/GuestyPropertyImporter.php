<?php

namespace App\Services\Guesty;

use App\Models\Property\Property;
use App\Models\Property\PropertyVariant;
use App\Models\Property\PropertyVariantAddress;
use App\Models\Property\PropertyVariantAvailability;
use App\Models\Property\PropertyBedType;
use App\Models\Property\PropertyVariantDescription;
use App\Models\Property\PropertyVariantGuestInfo;
use App\Models\Property\PropertyVariantPhoto;
use App\Models\Property\PropertyVariantPricing;
use App\Models\Property\PropertyVariantRoom;
use App\Models\Property\PropertyRoomType;
use App\Models\Property\PropertyTag;
use App\Models\Property\PropertySourceType;
use App\Models\Property\PropertyType;
use App\Models\SEO\ContentSeo;
use App\Models\Setting\SettingAmenity;
use App\Models\Setting\SettingPropertyFeature;
use App\Services\Constant\Property\PropertyAddressType;
use App\Services\Constant\Property\PropertyAdvanceNoticeUnit;
use App\Services\Constant\Property\PropertyAvailabilityType;
use App\Services\Constant\Property\PropertyCleaningFeeType;
use App\Services\Constant\Property\PropertyCleaningStatus;
use App\Services\Constant\Property\PropertyListingType;
use App\Services\Constant\Property\PropertyStatus;
use App\Services\Constant\Property\PropertyUnitType;
use App\Services\Constant\Storage\PathConstant;
use App\Services\Property\PropertyPhotoSyncService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestyPropertyImporter
{
    public bool $withPhotos = false;

    /**
     * Parents already looked up / created during this run.
     *
     * @var array<string, Property|null>
     */
    private array $parentCache = [];

    public function __construct(
        protected GuestyClient $client
    ) {}

    /**
     * Import Guesty listing.
     */
    public function import(array $listing): PropertyVariant
    {
        // Every Guesty API call runs before the database transaction, so a slow
        // or failing request never keeps the transaction (and its locks) open.
        $spaces = $this->fetchSpaces($listing);
        $guestyParent = $this->fetchGuestyParent($listing);

        try {
            $property = $this->importInTransaction($listing, $spaces, $guestyParent);
        } catch (\Throwable $e) {
            // A parent created in the rolled back transaction no longer exists.
            $this->parentCache = [];

            throw $e;
        }

        // Photo downloads also run outside the transaction.
        if ($this->withPhotos) {
            $this->syncPhotos($property, $listing);
            $this->syncParentPhotos($property);
        }

        return $property;
    }

    private function importInTransaction(
        array $listing,
        array $spaces,
        ?array $guestyParent
    ): PropertyVariant {
        return DB::transaction(function () use ($listing, $spaces, $guestyParent) {

            $property = PropertyVariant::updateOrCreate(
                ['guestyId' => $listing['_id']],
                [
                    'nickname' => $listing['title']
                        ?? $listing['nickname']
                        ?? 'Untitled',

                    'propertyTypeId' => $this->resolvePropertyTypeId(
                        $listing['propertyType'] ?? null
                    ),

                    'unitTypeId' => $this->mapUnitType(
                        $listing['type'] ?? 'SINGLE'
                    ),

                    'listingTypeId' => $this->mapListingType(
                        $listing['roomType'] ?? null
                    ),

                    'roomType' => $listing['roomType'] ?? null,

                    'occupancy' => $listing['accommodates'] ?? null,

                    'propertySize' => $listing['areaSquareFeet'] ?? null,

                    'statusId' => ($listing['isListed'] ?? false)
                        ? PropertyStatus::ACTIVE_ID
                        : PropertyStatus::INACTIVE_ID,

                    'cleaningStatusId' => $this->mapCleaningStatus(
                        $listing['cleaningStatus']['value'] ?? null
                    ),

                    'sourceTypeId' => PropertySourceType::firstOrCreate([
                        'name' => 'Guesty'
                    ])->id,

                    'currency' => $listing['prices']['currency'] ?? 'USD',

                    'guestyImportedAt' => now(),

                    'sourceCreatedAt' => !empty($listing['createdAt'])
                        ? Carbon::parse($listing['createdAt'])
                        : null,

                    'sourceUpdatedAt' => !empty($listing['updatedAt'])
                        ? Carbon::parse($listing['updatedAt'])
                        : null,
                ]
            );

            $this->syncParent($property, $listing, $guestyParent);

            $this->syncAddress($property, $listing);

            $this->syncParentAddress($property);

            $this->syncGuestInfo($property, $listing);

            $this->syncAvailability($property, $listing);

            $this->syncPricing($property, $listing);

            $this->syncDescriptions($property, $listing);

            $this->syncRooms($property, $listing);

            $this->syncAmenities($property, $listing);

            $this->syncTags($property, $listing);

            // Property Features: Bedroom, Bed, Bathroom
            $this->syncFeatures($property, $listing, $spaces);

            $this->syncParentFeatures($property);

            $this->syncSeo($property, $listing);

            return $property;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Spaces API
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieve spaces from Guesty.
     *
     * Endpoint:
     * GET /v1/properties/spaces/unit-type/{unitTypeId}
     */
    private function fetchSpaces(array $listing): array
    {
        $unitTypeId = $listing['bedArrangements']['unitType']
            ?? $listing['unitTypeId']
            ?? $listing['_id']
            ?? null;

        if (!$unitTypeId) {
            return [];
        }

        try {
            $response = $this->client->get(
                '/properties/spaces/unit-type/' . $unitTypeId
            );

            return $response['spaces'] ?? [];

        } catch (\Throwable $e) {
            report($e);

            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Property Features
    |--------------------------------------------------------------------------
    */

    /**
     * Sync property features from Guesty.
     *
     * Features:
     * - Bedroom
     * - Bed
     * - Bathroom
     *
     * Spaces API is the primary source.
     * Listing API is used as fallback.
     */
    private function syncFeatures(
        PropertyVariant $property,
        array $listing,
        array $spaces = []
    ): void {
        $hasSpaces = !empty($spaces);

        /*
         * Bedroom count
         */
        $bedrooms = null;

        if ($hasSpaces) {
            $bedrooms = collect($spaces)
                ->where('type', 'BEDROOM')
                ->count();
        } else {
            $bedrooms = $listing['bedrooms'] ?? null;
        }

        /*
         * Bathroom count
         */
        $bathrooms = null;

        if ($hasSpaces) {
            // A half bathroom (toilet + sink, no shower) counts as 0.5, like Guesty's own "bathrooms".
            $bathrooms = collect($spaces)->sum(fn ($space) => match ($space['type'] ?? null) {
                'FULL_BATHROOM' => 1,
                'HALF_BATHROOM' => 0.5,
                default => 0,
            });
        } else {
            $bathrooms = $listing['bathrooms'] ?? null;
        }

        /*
         * Bed count
         *
         * Sum all bed quantities from all spaces.
         * Does not include other items such as CRIB.
         */
        $beds = null;

        if ($hasSpaces) {
            $beds = collect($spaces)->sum(function ($space) {
                return collect($space['beds'] ?? [])
                    ->sum(fn ($quantity) => (int) $quantity);
            });
        } elseif (!empty($listing['listingRooms'])) {
            $beds = collect($listing['listingRooms'])
                ->sum(function ($room) {
                    return collect($room['beds'] ?? [])
                        ->sum(fn ($bed) => (int) ($bed['quantity'] ?? 0));
                });
        } elseif (!empty($listing['bedArrangements']['bedrooms'])) {
            $beds = collect($listing['bedArrangements']['bedrooms'])
                ->sum(function ($room) {
                    return collect($room['beds'] ?? [])
                        ->sum(fn ($quantity) => (int) $quantity);
                });
        }

        /*
         * Feature values
         */
        $values = [
            'Bedroom' => $bedrooms,
            'Bed' => $beds,
            'Bathroom' => $bathrooms,
        ];

        $syncData = [];

        foreach ($values as $name => $value) {
            if ($value === null) {
                continue;
            }

            $feature = SettingPropertyFeature::firstOrCreate(
                ['name' => $name],
                ['hasValue' => true]
            );

            $syncData[$feature->id] = [
                'value' => $value,
            ];
        }

        if (!empty($syncData)) {
            $property->features()->syncWithoutDetaching($syncData);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    private function syncSeo(
        PropertyVariant $property,
        array $listing
    ): void {
        $description = $listing['publicDescription'] ?? [];

        $title = $listing['title']
            ?? $listing['nickname']
            ?? 'Untitled';

        $data = [
            'title' => $title,
            'description' => $description['summary'] ?? null,
        ];

        // Existing SEO: keep its slug (URLs stay the same when the Guesty nickname changes)
        // and the fields an admin may have filled in (keywords, canonical URL, schema, ...).
        if ($seo = $property->seo()->first()) {
            $seo->update($data);

            return;
        }

        $base = Str::slug($listing['nickname'] ?? $title)
            ?: 'property-variant-' . $property->id;

        $property->seo()->create($data + [
            'info' => null,
            'slug' => $this->uniqueSlug($base),
            'metaKeyword' => null,
            'canonicalUrl' => null,
            'robotIndex' => true,
            'robotFollow' => true,
            'schemaMarkup' => null,
        ]);
    }

    /**
     * $base, or $base-2, $base-3, ... when the slug is already used by any content.
     */
    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 2;

        while (ContentSeo::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Address
    |--------------------------------------------------------------------------
    */

    private function syncAddress(
        PropertyVariant $property,
        array $listing
    ): void {
        $address = $listing['address'] ?? null;

        if (!$address) {
            return;
        }

        PropertyVariantAddress::updateOrCreate(
            [
                'propertyVariantId' => $property->id,
                'typeId' => PropertyAddressType::FULL_ID,
            ],
            [
                'address' => $address['full'] ?? null,
                'buildingName' => $address['apt'] ?? null,
                'latitude' => $address['lat'] ?? null,
                'longitude' => $address['lng'] ?? null,
                'zipCode' => $address['zipcode'] ?? null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guest Information
    |--------------------------------------------------------------------------
    */

    private function syncGuestInfo(
        PropertyVariant $property,
        array $listing
    ): void {
        $description = $listing['publicDescription'] ?? [];

        PropertyVariantGuestInfo::updateOrCreate(
            ['propertyVariantId' => $property->id],
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

    /*
    |--------------------------------------------------------------------------
    | Availability
    |--------------------------------------------------------------------------
    */

    private function syncAvailability(
        PropertyVariant $property,
        array $listing
    ): void {
        $terms = $listing['terms'] ?? [];
        $calendarRules = $listing['calendarRules'] ?? [];

        $advanceNotice = $calendarRules['advanceNotice']['defaultSettings']['hours']
            ?? null;

        PropertyVariantAvailability::updateOrCreate(
            ['propertyVariantId' => $property->id],
            [
                'defaultAvailabilityId' =>
                    ($calendarRules['defaultAvailability'] ?? null) === 'AVAILABLE'
                        ? PropertyAvailabilityType::ALWAYS_ID
                        : PropertyAvailabilityType::CUSTOM_ID,

                'advanceNoticeValue' => $advanceNotice,

                'advanceNoticeUnitId' => $advanceNotice !== null
                    ? PropertyAdvanceNoticeUnit::HOUR_ID
                    : null,

                'minLengthOfStay' => $terms['minNights'] ?? null,
                'maxLengthOfStay' => $terms['maxNights'] ?? null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing
    |--------------------------------------------------------------------------
    */

    private function syncPricing(
        PropertyVariant $property,
        array $listing
    ): void {
        $prices = $listing['prices'] ?? [];

        $cleaningFeeValue = $listing['financials']['cleaningFee']['value']
            ?? null;

        PropertyVariantPricing::updateOrCreate(
            ['propertyVariantId' => $property->id],
            [
                'weekdayBasePrice' => $prices['basePrice'] ?? null,

                'weekendBasePrice' => $prices['weekendBasePrice'] ?? null,

                'rateStrategy' => $listing['yieldManagement']['rateStrategy']['name']
                    ?? null,

                'cleaningFee' => $prices['cleaningFee']
                    ?? ($cleaningFeeValue['formula'] ?? null),

                'cleaningFeeTypeId' => $this->mapCleaningFeeType(
                    $cleaningFeeValue['multiplier'] ?? null
                ),

                'extraPersonFee' => $prices['extraPersonFee'] ?? null,

                'securityDepositFee' => $prices['securityDepositFee'] ?? null,

                'weeklyDiscount' => isset($prices['weeklyPriceFactor'])
                    ? round((1 - $prices['weeklyPriceFactor']) * 100, 2)
                    : 0,

                'monthlyDiscount' => isset($prices['monthlyPriceFactor'])
                    ? round((1 - $prices['monthlyPriceFactor']) * 100, 2)
                    : 0,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Descriptions
    |--------------------------------------------------------------------------
    */

    private function syncDescriptions(
        PropertyVariant $property,
        array $listing
    ): void {
        $description = $listing['publicDescription'] ?? [];

        PropertyVariantDescription::updateOrCreate(
            [
                'propertyVariantId' => $property->id,
                'channel' => 'primary',
                'language' => 'en',
            ],
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

    /*
    |--------------------------------------------------------------------------
    | Rooms
    |--------------------------------------------------------------------------
    */

    private function syncRooms(
        PropertyVariant $property,
        array $listing
    ): void {
        $listingRooms = $listing['listingRooms'] ?? [];

        if (empty($listingRooms)) {
            return;
        }

        $bedroomType = PropertyRoomType::firstOrCreate([
            'name' => 'Bedroom'
        ]);

        $property->rooms()->delete();

        foreach ($listingRooms as $room) {
            $beds = $room['beds'] ?? [];

            if (empty($beds)) {
                continue;
            }

            foreach ($beds as $bed) {
                $bedType = PropertyBedType::firstOrCreate([
                    'name' => $this->mapBedTypeName($bed['type'] ?? null)
                ]);

                PropertyVariantRoom::create([
                    'propertyVariantId' => $property->id,
                    'roomTypeId' => $bedroomType->id,
                    'label' => 'Bedroom ' . (($room['roomNumber'] ?? 0) + 1),
                    'bedTypeId' => $bedType->id,
                    'bedCount' => $bed['quantity'] ?? 1,
                    'order' => $room['roomNumber'] ?? 0,
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Amenities
    |--------------------------------------------------------------------------
    */

    private function syncAmenities(
        PropertyVariant $property,
        array $listing
    ): void {
        $names = $listing['amenities'] ?? [];

        if (empty($names)) {
            return;
        }

        $ids = collect($names)
            ->unique()
            ->map(function ($name) {
                return SettingAmenity::firstOrCreate([
                    'name' => $name
                ])->id;
            });

        $property->amenities()->sync($ids);
    }

    /*
    |--------------------------------------------------------------------------
    | Tags
    |--------------------------------------------------------------------------
    */

    private function syncTags(
        PropertyVariant $property,
        array $listing
    ): void {
        $names = $listing['tags'] ?? [];

        if (empty($names)) {
            return;
        }

        $ids = collect($names)
            ->unique()
            ->map(function ($name) {
                return PropertyTag::firstOrCreate([
                    'name' => $name
                ])->id;
            });

        $property->tags()->sync($ids);
    }

    /*
    |--------------------------------------------------------------------------
    | Photos
    |--------------------------------------------------------------------------
    */

    private function syncPhotos(
        PropertyVariant $property,
        array $listing
    ): void {
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

        $rows = [];

        foreach ($pictures as $index => $picture) {
            $url = $picture['original'] ?? null;

            if (!$url) {
                continue;
            }

            $bytes = $this->client->downloadImage($url);

            if (!$bytes) {
                continue;
            }

            $extension = pathinfo(
                (string) parse_url($url, PHP_URL_PATH),
                PATHINFO_EXTENSION
            ) ?: 'jpg';

            $filename = $property->guestyId . '-' . $index . '.' . $extension;

            file_put_contents($dirPath . $filename, $bytes);

            $rows[] = [
                'propertyVariantId' => $property->id,
                'path' => $filename,
                'caption' => $picture['caption'] ?? null,
                'order' => $index,
            ];
        }

        // Rows are saved only once every download is done: when a download fails
        // half way, no photo row exists and the next import tries again.
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                PropertyVariantPhoto::create($row);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Parent Address & Photos
    |--------------------------------------------------------------------------
    */

    private function syncParentAddress(PropertyVariant $variant): void
    {
        $parent = $variant->parentPropertyId
            ? Property::find($variant->parentPropertyId)
            : null;

        if ($parent) {
            app(PropertyPhotoSyncService::class)
                ->syncAddressesFromFirstVariant($parent);
        }
    }

    /**
     * Raise the parent's Bedroom / Bed / Bathroom to the highest value of its variants.
     */
    private function syncParentFeatures(PropertyVariant $variant): void
    {
        $parent = $variant->parentPropertyId
            ? Property::find($variant->parentPropertyId)
            : null;

        if ($parent) {
            app(PropertyPhotoSyncService::class)
                ->syncFeaturesFromVariants($parent);
        }
    }

    private function syncParentPhotos(PropertyVariant $variant): void
    {
        $parent = $variant->parentPropertyId
            ? Property::find($variant->parentPropertyId)
            : null;

        if ($parent) {
            app(PropertyPhotoSyncService::class)
                ->syncFromFirstVariant($parent);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Property Type
    |--------------------------------------------------------------------------
    */

    private function resolvePropertyTypeId(?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        return PropertyType::firstOrCreate([
            'name' => $name
        ])->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Parent Property
    |--------------------------------------------------------------------------
    |
    | A listing's parent Property comes from:
    |
    | 1. Guesty Multi-Unit: MTL_CHILD listing points to its MTL listing in mtl.p.
    | 2. The title: "White Horse Resort - Poolside Lodge"
    |    belongs to "White Horse Resort".
    |
    | An existing parent selected by admin is never overwritten.
    |
    */

    private function syncParent(
        PropertyVariant $variant,
        array $listing,
        ?array $guestyParent
    ): void {
        if ($variant->parentPropertyId) {
            return;
        }

        $parent = $this->parentFromGuesty($guestyParent, $listing)
            ?? $this->parentFromTitle($listing);

        if ($parent) {
            $variant->update([
                'parentPropertyId' => $parent->id
            ]);
        }
    }

    /**
     * Guesty data of a Multi-Unit child's parent, fetched before the transaction.
     * Returns ['guestyId' => ..., 'name' => ...], or null when there is no
     * Guesty parent to look for. 'name' is only fetched when the parent is not
     * in the database yet.
     */
    private function fetchGuestyParent(array $listing): ?array
    {
        if (($listing['type'] ?? null) !== 'MTL_CHILD') {
            return null;
        }

        // The parent of an existing variant is never changed: no API call needed.
        if (!empty($listing['_id']) && PropertyVariant::where('guestyId', $listing['_id'])
            ->whereNotNull('parentPropertyId')
            ->exists()) {
            return null;
        }

        $parentGuestyId = $listing['mtl']['p'] ?? null;

        // The listings list does not always include mtl.
        if (!$parentGuestyId && !empty($listing['_id'])) {
            try {
                $response = $this->client->get(
                    '/listings/' . $listing['_id'],
                    ['fields' => '_id mtl']
                );

                $parentGuestyId = $response['mtl']['p'] ?? null;

            } catch (\Throwable $e) {
                $parentGuestyId = null;
            }
        }

        if (!$parentGuestyId) {
            return null;
        }

        $parent = ['guestyId' => $parentGuestyId, 'name' => null];

        if (isset($this->parentCache['g:' . $parentGuestyId])
            || Property::where('guestyId', $parentGuestyId)->exists()) {
            return $parent;
        }

        try {
            $data = $this->client->get(
                '/listings/' . $parentGuestyId,
                ['fields' => '_id title nickname type']
            );

            $parent['name'] = trim((string) (
                $data['title'] ?? $data['nickname'] ?? ''
            ));

        } catch (\Throwable $e) {
            // No name: an existing parent can still be found by its guestyId.
        }

        return $parent;
    }

    /**
     * Find or create the parent fetched by fetchGuestyParent(). Database only.
     */
    private function parentFromGuesty(?array $guestyParent, array $listing): ?Property
    {
        if (!$guestyParent) {
            return null;
        }

        return $this->parentCache['g:' . $guestyParent['guestyId']]
            ??= $this->findOrCreateGuestyParent(
                $guestyParent['guestyId'],
                $guestyParent['name'],
                $listing
            );
    }

    private function findOrCreateGuestyParent(
        string $guestyId,
        ?string $name,
        array $child
    ): ?Property {
        if ($existing = Property::where('guestyId', $guestyId)->first()) {
            return $existing;
        }

        if ($name === null || $name === '') {
            return null;
        }

        // Adopt an existing parent with the same name.
        if ($byName = Property::where('nickname', $name)->first()) {
            if (!$byName->guestyId) {
                $byName->update([
                    'guestyId' => $guestyId
                ]);
            }

            return $byName;
        }

        return $this->createParent(
            $name,
            $child,
            $guestyId
        );
    }

    private function parentFromTitle(array $listing): ?Property
    {
        $parts = preg_split(
            '/\s+[-–—]\s+/u',
            trim((string) ($listing['title'] ?? '')),
            2
        );

        if (!$parts || count($parts) < 2) {
            return null;
        }

        $name = trim($parts[0]);

        if (mb_strlen($name) < 2) {
            return null;
        }

        return $this->parentCache['n:' . mb_strtolower($name)]
            ??= Property::where('nickname', $name)->first()
                ?? $this->createParent($name, $listing);
    }

    private function createParent(
        string $name,
        array $child,
        ?string $guestyId = null
    ): Property {
        $parent = Property::create([
            'guestyId' => $guestyId,
            'nickname' => $name,

            'propertyTypeId' => $this->resolvePropertyTypeId(
                $child['propertyType'] ?? null
            ),

            'unitTypeId' => PropertyUnitType::MULTI_UNIT_ID,

            'listingTypeId' => $this->mapListingType(
                $child['roomType'] ?? null
            ),

            'statusId' => PropertyStatus::ACTIVE_ID,

            'sourceTypeId' => PropertySourceType::firstOrCreate([
                'name' => 'Guesty'
            ])->id,

            'currency' => $child['prices']['currency'] ?? 'USD',
        ]);

        $this->syncParentSeo($parent);

        return $parent;
    }

    private function syncParentSeo(Property $parent): void
    {
        if ($parent->seo()->exists()) {
            return;
        }

        $base = Str::slug($parent->nickname)
            ?: 'property-' . $parent->id;

        $parent->seo()->create([
            'title' => $parent->nickname,
            'slug' => $this->uniqueSlug($base),
            'robotIndex' => true,
            'robotFollow' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mappers
    |--------------------------------------------------------------------------
    */

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

            'waiting_for_inspection',
            'inspection' => PropertyCleaningStatus::WAITING_FOR_INSPECTION_ID,

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

        return ucwords(
            strtolower(
                str_replace('_', ' ', $type)
            )
        );
    }
}
