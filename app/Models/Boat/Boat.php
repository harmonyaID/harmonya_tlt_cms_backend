<?php

namespace App\Models\Boat;

use App\Models\BaseModel;
use App\Parser\Boat\BoatParser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boat extends BaseModel
{
    use SoftDeletes;

    protected $table = 'boats';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'departureTimesFromBali' => 'array',
        'departureTimesFromLembongan' => 'array',
        'discountPercentage' => 'integer',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = BoatParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function photos(): HasMany
    {
        return $this->hasMany(BoatPhoto::class, 'boatId')->orderBy('order');
    }

    public function types(): HasMany
    {
        return $this->hasMany(BoatType::class, 'boatId');
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
                $query->where(function ($search) use ($request) {
                    $search->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('routeFrom', 'LIKE', "%$request->search%")
                        ->orWhere('routeTo', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('id', 'DESC');
    }
}