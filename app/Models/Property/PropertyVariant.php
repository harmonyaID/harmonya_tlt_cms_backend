<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use App\Models\SEO\ContentSeo;
use App\Models\Setting\SettingAmenity;
use App\Models\Setting\SettingPropertyFeature;
use App\Models\Traits\HasDateRangeFilter;
use App\Models\Traits\HasMultiValueFilter;
use App\Models\Traits\HasSeoSlugScope;
use App\Parser\Property\PropertyVariantParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyVariant extends BaseModel
{
    use SoftDeletes;
    use HasDateRangeFilter;
    use HasMultiValueFilter;
    use HasSeoSlugScope;

    protected $table = 'property_variants';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'parentPropertyId' => 'integer',
        'propertyTypeId' => 'integer',
        'unitTypeId' => 'integer',
        'listingTypeId' => 'integer',
        'occupancy' => 'integer',
        'bedroomCount' => 'integer',
        'bathroomCount' => 'integer',
        'propertySize' => 'decimal:2',
        'statusId' => 'integer',
        'cleaningStatusId' => 'integer',
        'sourceTypeId' => 'integer',
        'guestyImportedAt' => 'datetime',
        'isPopular' => 'boolean',
        'whyYoullLoveThis' => 'array',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = PropertyVariantParser::class;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'parentPropertyId');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'propertyTypeId');
    }

    public function sourceType(): BelongsTo
    {
        return $this->belongsTo(PropertySourceType::class, 'sourceTypeId');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(PropertyVariantAddress::class, 'propertyVariantId');
    }

    public function guestInfo(): HasOne
    {
        return $this->hasOne(PropertyVariantGuestInfo::class, 'propertyVariantId');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(PropertyVariantRoom::class, 'propertyVariantId')->orderBy('order');
    }

    public function availability(): HasOne
    {
        return $this->hasOne(PropertyVariantAvailability::class, 'propertyVariantId');
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(PropertyVariantPricing::class, 'propertyVariantId');
    }

    public function descriptions(): HasMany
    {
        return $this->hasMany(PropertyVariantDescription::class, 'propertyVariantId');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyVariantPhoto::class, 'propertyVariantId')->orderBy('order');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(SettingAmenity::class, 'property_variant_amenity', 'propertyVariantId', 'amenityId');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(PropertyTag::class, 'property_variant_tag', 'propertyVariantId', 'tagId');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(SettingPropertyFeature::class, 'property_variant_feature', 'propertyVariantId', 'featureId')
            ->withPivot('value')
            ->orderBy('setting_property_features.order');
    }

    public function relatedPropertyVariants(): BelongsToMany
    {
        return $this->belongsToMany(PropertyVariant::class, 'property_variant_related', 'propertyVariantId', 'relatedPropertyVariantId')
            ->withPivot('order')
            ->orderBy('property_variant_related.order');
    }

    public function seo()
    {
        return $this->morphOne(ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }

    public function acf()
    {
        return $this->morphMany(\App\Models\Acf\ContentAcf::class, 'contentable', 'contentableType', 'contentableId');
    }

    public function floorplanImageUrl(): ?string
    {
        if (!$this->floorplanImage) {
            return null;
        }

        return asset('storage/' . PathConstant::IMAGES_PROPERTY_FLOORPLAN . $this->floorplanImage);
    }
    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where(function ($search) use ($request) {
                    $search->where('nickname', 'LIKE', "%$request->search%")
                        ->orWhere('guestyId', $request->search);
                });
            }

            if ($request->has('guestyId') && $request->guestyId) {
                $query->where('guestyId', trim((string) $request->guestyId));
            }

            if ($request->has('guestyIds') && $request->guestyIds) {
                $query->whereIn('guestyId', $this->toValueArray($request->guestyIds));
            }

            $this->applyFeatureFilter($query, $request, 'bedrooms', 'bedroomsMin', 'Bedroom');
            $this->applyFeatureFilter($query, $request, 'bathrooms', 'bathroomsMin', 'Bathroom');

            if ($request->has('statusId') && $request->statusId) {
                $query->where('statusId', $request->statusId);
            }
            
            if ($request->has('propertyVariantIds') && $request->propertyVariantIds) {
                $query->whereIn('id', $this->toValueArray($request->propertyVariantIds));
            }

            if ($request->has('parentPropertyIds') && $request->parentPropertyIds) {
                $query->whereIn('parentPropertyId', $this->toValueArray($request->parentPropertyIds));
            }

            if ($request->has('propertyTypeIds') && $request->propertyTypeIds) {
                $query->whereIn('propertyTypeId', $this->toValueArray($request->propertyTypeIds));
            }

            // Collection = tag (collectionIds is an alias of tagIds)
            $collectionIds = $request->tagIds ?: $request->collectionIds;
            if ($collectionIds) {
                $query->whereHas('tags', function ($tag) use ($collectionIds) {
                    $tag->whereIn('property_tags.id', $this->toValueArray($collectionIds));
                });
            }

            if ($request->has('sourceTypeIds') && $request->sourceTypeIds) {
                $query->whereIn('sourceTypeId', $this->toValueArray($request->sourceTypeIds));
            }

            // "Area" = free-text search against the property's address (e.g. "Jungutbatu")
            if ($request->has('area') && strlen($request->area) > 1) {
                $query->whereHas('addresses', function ($address) use ($request) {
                    $address->where('address', 'LIKE', "%$request->area%");
                });
            }

            if ($request->has('occupancyMin') && $request->occupancyMin) {
                $query->where('occupancy', '>=', $request->occupancyMin);
            }

            if ($request->has('occupancyMax') && $request->occupancyMax) {
                $query->where('occupancy', '<=', $request->occupancyMax);
            }

            // "Language" = filter by which language a property's description was written in
            if ($request->has('language') && $request->language) {
                $query->whereHas('descriptions', function ($description) use ($request) {
                    $description->where('language', $request->language);
                });
            }

            $this->applyDateRangeFilter($query, $request);
        })->orderBy('id', 'DESC');
    }

    /**
     * Value of a feature (e.g. "Bedroom", "Bathroom") as a number, null when not set.
     * Uses the loaded "features" relation.
     */
    public function featureValue(string $name): int|float|null
    {
        $feature = $this->features->firstWhere('name', $name);
        $value = $feature?->pivot?->value;

        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        return $value + 0;
    }

    private function applyFeatureFilter($query, $request, string $exactParam, string $minParam, string $featureName): void
    {
        if ($request->has($exactParam) && $request->{$exactParam} !== null && $request->{$exactParam} !== '') {
            $numbers = array_filter($this->toValueArray($request->{$exactParam}), 'is_numeric');

            if ($numbers) {
                $query->whereHas('features', function ($feature) use ($featureName, $numbers) {
                    $feature->where('setting_property_features.name', $featureName)
                        ->where(function ($or) use ($numbers) {
                            foreach ($numbers as $number) {
                                $or->orWhereRaw('CAST(property_variant_feature.value AS DECIMAL(6,1)) = ?', [(float) $number]);
                            }
                        });
                });
            }
        }

        if ($request->has($minParam) && is_numeric($request->{$minParam})) {
            $query->whereHas('features', function ($feature) use ($featureName, $request, $minParam) {
                $feature->where('setting_property_features.name', $featureName)
                    ->whereRaw('CAST(property_variant_feature.value AS DECIMAL(6,1)) >= ?', [(float) $request->{$minParam}]);
            });
        }
    }
}
