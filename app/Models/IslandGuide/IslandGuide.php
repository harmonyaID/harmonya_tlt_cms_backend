<?php

namespace App\Models\IslandGuide;

use App\Models\BaseModel;
use App\Models\SEO\ContentSeo;
use App\Models\Traits\HasDateRangeFilter;
use App\Models\Traits\HasMultiValueFilter;
use App\Parser\IslandGuide\IslandGuideParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class IslandGuide extends BaseModel
{
    use SoftDeletes;
    use HasDateRangeFilter;
    use HasMultiValueFilter;

    protected $table = 'island_guides';
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

    public $parserClass = IslandGuideParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function type(): BelongsTo
    {
        return $this->belongsTo(IslandGuideType::class, 'islandGuideTypeId');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(IslandGuideArea::class, 'islandGuideAreaId');
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
        return $this->hasMany(IslandGuidePhoto::class, 'islandGuideId')->orderBy('order');
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

            if ($request->has('islandGuideTypeIds') && $request->islandGuideTypeIds) {
                $query->whereIn('islandGuideTypeId', $this->toValueArray($request->islandGuideTypeIds));
            }

            if ($request->has('islandGuideAreaIds') && $request->islandGuideAreaIds) {
                $query->whereIn('islandGuideAreaId', $this->toValueArray($request->islandGuideAreaIds));
            }

            if ($request->has('isActive') && $request->isActive !== null && $request->isActive !== '') {
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
        return Storage::disk('public')->url(PathConstant::IMAGES_ISLAND_GUIDE . $this->thumbnail);
    }

    public function mapImageUrl()
    {
        if (!$this->mapImage) return null;
        return Storage::disk('public')->url(PathConstant::IMAGES_ISLAND_GUIDE . $this->mapImage);
    }

    public function catalogsWithUrl()
    {
        return collect($this->catalogs ?? [])
            ->map(function ($catalog) {
                return [
                    'id'   => $catalog['id'],
                    'name' => $catalog['name'],
                    'file' => Storage::disk('public')->url(
                        PathConstant::PDF_ISLAND_GUIDE . $catalog['file']
                    ),
                ];
            })
            ->values()
            ->all();
    }
}