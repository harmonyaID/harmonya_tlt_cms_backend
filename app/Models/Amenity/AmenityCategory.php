<?php

namespace App\Models\Amenity;

use App\Models\BaseModel;
use App\Parser\Amenity\AmenityCategoryParser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmenityCategory extends BaseModel
{
    use SoftDeletes;

    protected $table = 'amenity_categories';
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

    public $parserClass = AmenityCategoryParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class, 'categoryId')->orderBy('order');
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

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }
}