<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use App\Models\SEO\ContentSeo;
use App\Models\Setting\SettingAmenity;
use App\Models\Setting\SettingPropertyFeature;
use App\Models\Traits\HasDateRangeFilter;
use App\Parser\Property\PropertyParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends BaseModel
{
    use SoftDeletes;
    use HasDateRangeFilter;


    protected $table = 'properties';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
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
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = PropertyParser::class;

    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'propertyTypeId');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(PropertyAddress::class, 'propertyId');
    }

    public function guestInfo(): HasOne
    {
        return $this->hasOne(PropertyGuestInfo::class, 'propertyId');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(PropertyRoom::class, 'propertyId')->orderBy('order');
    }

    public function availability(): HasOne
    {
        return $this->hasOne(PropertyAvailability::class, 'propertyId');
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(PropertyPricing::class, 'propertyId');
    }

    public function descriptions(): HasMany
    {
        return $this->hasMany(PropertyDescription::class, 'propertyId');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class, 'propertyId')->orderBy('order');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(SettingAmenity::class, 'property_amenity', 'propertyId', 'amenityId');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(PropertyTag::class, 'property_tag', 'propertyId', 'tagId');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(SettingPropertyFeature::class, 'property_feature', 'propertyId', 'featureId')
            ->withPivot('value')
            ->orderBy('setting_property_features.order');
    }

    public function relatedProperties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_related', 'propertyId', 'relatedPropertyId')
            ->withPivot('order')
            ->orderBy('property_related.order');
    }

    public function seo()
    {
        return $this->morphOne(ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }

    public function acf()
    {
        return $this->morphMany(\App\Models\Acf\ContentAcf::class, 'contentable', 'contentableType', 'contentableId');
    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('nickname', 'LIKE', "%$request->search%");
            }

            if ($request->has('statusId') && $request->statusId) {
                $query->where('statusId', $request->statusId);
            }

            if ($request->has('propertyTypeId') && $request->propertyTypeId) {
                $query->where('propertyTypeId', $request->propertyTypeId);
            }

            if ($request->has('tagId') && $request->tagId) {
                $query->whereHas('tags', function ($tag) use ($request) {
                    $tag->where('property_tags.id', $request->tagId);
                });
            }

            if ($request->has('sourceTypeId') && $request->sourceTypeId) {
                $query->where('sourceTypeId', $request->sourceTypeId);
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
}