<?php

namespace App\Models\Page;

use App\Models\Access\Traits\HasAccession;
use App\Models\BaseModel;
use App\Models\HasActivation;
use App\Models\Setting\SettingCountry;
use App\Models\Traits\HasDateRangeFilter;
use App\Parser\Page\PageParser;
use App\Parser\Staff\StaffParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Page extends BaseModel
{
    use HasActivation;
    use HasAccession;
    use HasDateRangeFilter;

    protected $table = 'pages';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
        'isActive' => 'boolean',
        'isSuperadmin' => 'boolean',
    ];

    public $parserClass = PageParser::class;


    /** --- RELATIONSHIPS --- */
    // public function getLanguage()
    // {
    //     return $this->belongsTo(Language::class, 'language', 'code');
    // }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Staff\Staff::class, 'createdBy');
    }

    public function seo()
    {
        return $this->morphOne(\App\Models\SEO\ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }

    // public function photos()
    // {
    //     return $this->hasMany(PagePhoto::class, 'pageId');
    // }

    public function acf()
    {
        return $this->morphMany(\App\Models\Acf\ContentAcf::class, 'contentable', 'contentableType', 'contentableId');
    }



    /** --- SCOPES --- */

    public function scopeFilter($query, $request)
    {
        return $query->orderBy('groupId', 'ASC')->groupBy('groupId')->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {

                $query->where(function ($search) use ($request) {
                    $search->where("title", "LIKE", "%$request->search%")
                        ->orWhere("shortDescription", "LIKE", "%$request->search%")
                        ->orWhere("status", "LIKE", "%$request->search%");
                });
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('locale') && $request->locale) {
                $query->where('locale', $request->locale);
            }

            $this->applyDateRangeFilter($query, $request);

        });
    }

    public function scopeLanguage($query, $request)
    {
        return $query->where('groupId', $request->groupId)->where('locale', $request->locale);
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    // public function photoUrl($photo)
    // {
    //     $photo = PathConstant::IMAGES_PAGE . $photo;
    //     return  Storage::disk('public')->url($photo);
    // }

}