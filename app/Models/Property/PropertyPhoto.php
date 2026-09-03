<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyPhoto extends BaseModel
{
    protected $table = 'property_photos';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'propertyId' => 'integer',
        'order' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'propertyId');
    }

    public function pathUrl()
    {
        if (!$this->path) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_PROPERTY_PHOTO . $this->path);
    }
}