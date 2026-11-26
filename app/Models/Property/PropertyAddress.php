<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyAddress extends BaseModel
{
    protected $table = 'property_addresses';
    protected $guarded = ['id'];

    /** The area is shown with every address. */
    protected $with = ['area'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'propertyId' => 'integer',
        'typeId' => 'integer',
        'areaId' => 'integer',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'propertyId');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(PropertyArea::class, 'areaId');
    }
}
