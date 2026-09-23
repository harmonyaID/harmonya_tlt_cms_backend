<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Models\Property\Property;
use App\Models\SEO\ContentSeo;
use App\Models\Traits\HasSeoSlugScope;
use App\Parser\Experience\ExperienceAreaParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ExperienceArea extends BaseModel
{
    use SoftDeletes;
    use HasSeoSlugScope;

    protected $table = 'experience_areas';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'customInformations' => 'array',
        'experiencePlayIds' => 'array',
        'experienceEatIds' => 'array',
        'propertyIds' => 'array',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = ExperienceAreaParser::class;

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExperienceType::class, 'experienceTypeId');
    }

    public function getExperiencePlayData()
    {
        return Experience::whereIn('id', $this->experiencePlayIds ?? [])->get();
    }

    public function getExperienceEatData()
    {
        return Experience::whereIn('id', $this->experienceEatIds ?? [])->get();
    }

    public function getPropertyData()
    {
        return Property::whereIn('id', $this->propertyIds ?? [])->get();
    }

    public function seo()
    {
        return $this->morphOne(
            ContentSeo::class,
            'contentable',
            'contentableType',
            'contentableId'
        );
    }

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

    public function featuredImageUrl()
    {
        if (!$this->featuredImage) {
            return null;
        }

        return Storage::disk('public')->url(
            PathConstant::IMAGES_EXPERIENCE_AREA . $this->featuredImage
        );
    }

    public function mapsImageUrl()
    {
        if (!$this->mapsImage) {
            return null;
        }

        return Storage::disk('public')->url(
            PathConstant::IMAGES_EXPERIENCE_AREA . $this->mapsImage
        );
    }

    public function bannerUrl()
    {
        if (!$this->banner) {
            return null;
        }

        return Storage::disk('public')->url(
            PathConstant::IMAGES_EXPERIENCE_AREA . $this->banner
        );
    }
}
