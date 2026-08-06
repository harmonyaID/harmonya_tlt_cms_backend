<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Models\SEO\ContentSeo;
use App\Models\Traits\HasDateRangeFilter;
use App\Parser\Experience\ExperienceParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Experience extends BaseModel
{
    use SoftDeletes;
    use HasDateRangeFilter;

    protected $table = 'experiences';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'catalogs' => 'array',
        'isActive' => 'boolean',
        'showInquiry' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = ExperienceParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExperienceType::class, 'experienceTypeId');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(ExperienceArea::class, 'experienceAreaId');
    }

    public function seo()
    {
        return $this->morphOne(ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }

    public function acf()
    {
        return $this->morphMany(\App\Models\Acf\ContentAcf::class, 'contentable', 'contentableType', 'contentableId');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ExperiencePhoto::class, 'experienceId')->orderBy('order');
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
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('description', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('experienceTypeId') && $request->experienceTypeId) {
                $query->where('experienceTypeId', $request->experienceTypeId);
            }

            if ($request->has('experienceAreaId') && $request->experienceAreaId) {
                $query->where('experienceAreaId', $request->experienceAreaId);
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

            if ($request->has('locale') && $request->locale) {
                $query->where('locale', $request->locale);
            }
            $this->applyDateRangeFilter($query, $request);
        })->orderBy('id', 'DESC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function thumbnailUrl()
    {
        if (!$this->thumbnail) return null;
        return Storage::disk('public')->url(PathConstant::IMAGES_EXPERIENCE . $this->thumbnail);
    }

    public function mapImageUrl()
    {
        if (!$this->mapImage) return null;
        return Storage::disk('public')->url(PathConstant::IMAGES_EXPERIENCE . $this->mapImage);
    }

    public function catalogsWithUrl()
    {
        return collect($this->catalogs ?? [])
            ->map(function ($catalog) {
                return [
                    'id'   => $catalog['id'],
                    'name' => $catalog['name'],
                    'file' => Storage::disk('public')->url(
                        PathConstant::PDF_EXPERIENCE . $catalog['file']
                    ),
                ];
            })
            ->values()
            ->all();
    }
}