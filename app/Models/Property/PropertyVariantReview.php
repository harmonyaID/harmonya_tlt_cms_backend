<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use App\Parser\Property\PropertyVariantReviewParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyVariantReview extends BaseModel
{
    use SoftDeletes;

    protected $table = 'property_variant_reviews';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'propertyVariantId' => 'integer',
        'rating' => 'integer',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = PropertyVariantReviewParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function propertyVariant(): BelongsTo
    {
        return $this->belongsTo(PropertyVariant::class, 'propertyVariantId');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyVariantReviewPhoto::class, 'reviewId');
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
                    $search->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('review', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('propertyVariantId') && $request->propertyVariantId) {
                $query->where('propertyVariantId', $request->propertyVariantId);
            }

            if ($request->has('isActive') && $request->isActive !== null && $request->isActive !== '') {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('id', 'DESC');
    }
}
