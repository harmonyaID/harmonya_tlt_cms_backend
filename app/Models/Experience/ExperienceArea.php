<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Parser\Experience\ExperienceAreaParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ExperienceArea extends BaseModel
{
    use SoftDeletes;

    protected $table = 'experience_areas';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = ExperienceAreaParser::class;

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

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function featuredImageUrl()
    {
        if (!$this->featuredImage) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_EXPERIENCE_AREA . $this->featuredImage);
    }

    public function bannerUrl()
    {
        if (!$this->banner) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_EXPERIENCE_AREA . $this->banner);
    }
}
