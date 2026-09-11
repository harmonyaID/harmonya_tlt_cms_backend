<?php

namespace App\Models\IslandGuide;

use App\Models\BaseModel;
use App\Models\SEO\ContentSeo;
use App\Parser\IslandGuide\IslandGuideTypeParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class IslandGuideType extends BaseModel
{
    use SoftDeletes;

    protected $table = 'island_guide_types';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = IslandGuideTypeParser::class;

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('name', 'LIKE', "%$request->search%");
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

        return Storage::disk('public')->url(PathConstant::IMAGES_ISLAND_GUIDE_TYPE . $this->featuredImage);
    }

    public function bannerUrl()
    {
        if (!$this->banner) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_ISLAND_GUIDE_TYPE . $this->banner);
    }

    public function seo()
    {
        return $this->morphOne(ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }
}
