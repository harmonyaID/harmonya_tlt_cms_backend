<?php

namespace App\Models\Offer;

use App\Models\Acf\ContentAcf;
use App\Models\BaseModel;
use App\Models\Property\Property;
use App\Models\SEO\ContentSeo;
use App\Models\Traits\HasDateRangeFilter;
use App\Models\Traits\HasMultiValueFilter;
use App\Models\Traits\HasSlugLookup;
use App\Parser\Offer\OfferParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Offer extends BaseModel
{
    use SoftDeletes;
    use HasDateRangeFilter;
    use HasMultiValueFilter;
    use HasSlugLookup;

    protected $table = 'offers';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'isActive' => 'boolean',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
        'publishedAt' => 'datetime',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = OfferParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'offer_property', 'offerId', 'propertyId');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(OfferCategory::class, 'categoryId');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(OfferTag::class, 'offer_tag', 'offerId', 'tagId');
    }

    public function seo()
    {
        return $this->morphOne(ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }

    public function acf()
    {
        return $this->morphMany(ContentAcf::class, 'contentable', 'contentableType', 'contentableId');
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where(function ($search) use ($request) {
                    $search->where('title', 'LIKE', "%$request->search%")
                        ->orWhere('excerpt', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('propertyId') && $request->propertyId) {
                $query->whereHas('properties', function ($property) use ($request) {
                    $property->where('properties.id', $request->propertyId);
                });
            }

            if ($request->has('categoryIds') && $request->categoryIds) {
                $query->whereIn('categoryId', $this->toValueArray($request->categoryIds));
            }

            if ($request->has('tagIds') && $request->tagIds) {
                $query->whereHas('tags', function ($tag) use ($request) {
                    $tag->whereIn('offer_tags.id', $this->toValueArray($request->tagIds));
                });
            }

            if ($request->has('locale') && $request->locale) {
                $query->where('locale', $request->locale);
            }

            if ($request->has('isActive') && $request->isActive !== null && $request->isActive !== '') {
                $query->where('isActive', $request->isActive);
            }

            $this->applyDateRangeFilter($query, $request, 'publishedAt');

        })->orderBy('id', 'DESC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function thumbnailUrl()
    {
        if (!$this->thumbnail) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_OFFER . $this->thumbnail);
    }
}
