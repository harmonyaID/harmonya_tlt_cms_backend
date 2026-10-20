<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyVariantAvailability extends BaseModel
{
    protected $table = 'property_variant_availabilities';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'propertyVariantId' => 'integer',
        'defaultAvailabilityId' => 'integer',
        'advanceNoticeValue' => 'integer',
        'advanceNoticeUnitId' => 'integer',
        'preparationTimeValue' => 'integer',
        'maxNightsPerYear' => 'integer',
        'minLengthOfStay' => 'integer',
        'maxLengthOfStay' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
    ];

    public function propertyVariant(): BelongsTo
    {
        return $this->belongsTo(PropertyVariant::class, 'propertyVariantId');
    }
}