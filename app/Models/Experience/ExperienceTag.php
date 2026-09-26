<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExperienceTag extends BaseModel
{
    protected $table = 'experience_tags';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(
            Experience::class,
            'experience_tag',
            'tagId',
            'experienceId'
        );
    }
}