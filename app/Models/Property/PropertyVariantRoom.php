<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyVariantRoom extends BaseModel
{
    protected $table = 'property_variant_rooms';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'propertyVariantId' => 'integer',
        'roomTypeId' => 'integer',
        'bedTypeId' => 'integer',
        'bedCount' => 'integer',
        'order' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
    ];

    public function propertyVariant(): BelongsTo
    {
        return $this->belongsTo(PropertyVariant::class, 'propertyVariantId');
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(PropertyRoomType::class, 'roomTypeId');
    }

    public function bedType(): BelongsTo
    {
        return $this->belongsTo(PropertyBedType::class, 'bedTypeId');
    }
}