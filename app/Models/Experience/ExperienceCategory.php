<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Parser\Experience\ExperienceCategoryParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExperienceCategory extends BaseModel
{
    use SoftDeletes;

    protected $table = 'experience_categories';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = ExperienceCategoryParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExperienceType::class, 'experienceTypeId');
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('name', 'LIKE', "%$request->search%");
            }

            if ($request->has('experienceTypeId') && $request->experienceTypeId) {
                $query->where('experienceTypeId', $request->experienceTypeId);
            }

        })->orderBy('id', 'ASC');
    }
}