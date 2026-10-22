<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Parser\Setting\SettingAmenityParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Public URL of the uploaded icon image (null when no icon was uploaded).
     */
    public function iconUrl(): ?string
    {
        if (!$this->icon) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_AMENITY . $this->icon);
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

            if ($request->has('isPublish') && $request->isPublish !== null && $request->isPublish !== '') {
                $query->where('isPublish', $request->isPublish);
            }

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }
}