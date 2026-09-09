<?php

namespace App\Models\IslandGuide;

use App\Models\BaseModel;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class IslandGuidePhoto extends BaseModel
{
    protected $table = 'island_guide_photos';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function islandGuide(): BelongsTo
    {
        return $this->belongsTo(IslandGuide::class, 'islandGuideId');
    }

    public function photoUrl()
    {
        return Storage::disk('public')->url(PathConstant::IMAGES_ISLAND_GUIDE . $this->photo);
    }
}