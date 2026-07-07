<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ExperiencePhoto extends BaseModel
{
    protected $table = 'experience_photos';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class, 'experienceId');
    }

    public function photoUrl()
    {
        return Storage::disk('public')->url(PathConstant::IMAGES_EXPERIENCE . $this->photo);
    }
}