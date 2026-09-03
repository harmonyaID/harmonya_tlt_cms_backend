<?php

namespace App\Models\Boat;

use App\Models\BaseModel;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BoatPhoto extends BaseModel
{
    protected $table = 'boat_photos';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'order'          => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
    ];

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class, 'boatId');
    }

    public function photoUrl(): string
    {
        return Storage::disk('public')->url(PathConstant::IMAGES_BOAT . $this->photo);
    }
}