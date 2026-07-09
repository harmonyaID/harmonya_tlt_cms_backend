<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Parser\Experience\ExperienceParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Experience extends BaseModel
{
    use SoftDeletes;

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExperienceCategory::class, 'experienceCategoryId');
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

            if ($request->has('experienceCategoryId') && $request->experienceCategoryId) {
                $query->where('experienceCategoryId', $request->experienceCategoryId);
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }
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
