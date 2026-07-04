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

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class, 'boatId');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function photoUrl()
    {
        return Storage::disk('public')->url(PathConstant::IMAGES_BOAT . $this->photo);
    }
}