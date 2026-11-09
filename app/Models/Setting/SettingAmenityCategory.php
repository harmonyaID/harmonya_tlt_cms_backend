<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Parser\Setting\SettingAmenityCategoryParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SettingAmenityCategory extends BaseModel
{
    use SoftDeletes;

    protected $table = 'setting_amenity_categories';

    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'order' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = SettingAmenityCategoryParser::class;

    public function amenities(): HasMany
    {
        return $this->hasMany(SettingAmenity::class, 'categoryId')->orderBy('order');
    }

    public function iconUrl(): ?string
    {
        if (!$this->icon) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_AMENITY_CATEGORY . $this->icon);
    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('name', 'LIKE', "%$request->search%");
            }
        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }
}
