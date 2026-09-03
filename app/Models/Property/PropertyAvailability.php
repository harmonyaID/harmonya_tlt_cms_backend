<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyAvailability extends BaseModel
{
    protected $table = 'property_availabilities';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'propertyId' => 'integer',
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

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'propertyId');
    }
}