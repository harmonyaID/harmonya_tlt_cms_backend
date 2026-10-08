<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Models\Traits\HasMultiValueFilter;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExperienceTag extends BaseModel
{
    use SoftDeletes;
    use HasMultiValueFilter;

    protected $table = 'experience_tags';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(
            Experience::class,
            'experience_tag',
            'tagId',
            'experienceId'
        );
    }

    public function scopeFilter($query, $request)
    {
        return $query
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(
                    'name',
                    'LIKE',
                    '%' . $request->search . '%'
                );
            })
            ->orderBy('id', 'DESC');
    }
}