<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Parser\Setting\SettingAmenityParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingAmenity extends BaseModel
{
    use SoftDeletes;

    protected $table = 'setting_amenities';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'categoryId' => 'integer',
        'isPopular' => 'boolean',
        'order' => 'integer',
        'isPublish' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = SettingAmenityParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(SettingAmenityCategory::class, 'categoryId');
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

            if ($request->has('categoryId') && $request->categoryId) {
                $query->where('categoryId', $request->categoryId);
            }

            if ($request->has('isPopular') && $request->isPopular !== null) {
                $query->where('isPopular', $request->isPopular);
            }

            if ($request->has('isPublish') && $request->isPublish !== null) {
                $query->where('isPublish', $request->isPublish);
            }

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }
}