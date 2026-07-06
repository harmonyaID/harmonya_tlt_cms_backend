<?php

namespace App\Models\Boat;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoatCustomInformation extends BaseModel
{
    protected $table = 'boat_custom_informations';
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
}