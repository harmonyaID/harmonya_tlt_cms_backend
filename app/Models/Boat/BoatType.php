<?php

namespace App\Models\Boat;

use App\Models\BaseModel;
use App\Parser\Boat\BoatTypeParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoatType extends BaseModel
{
    use SoftDeletes;

    protected $table = 'boat_types';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'priceReturnAdult' => 'float',
        'priceReturnChild' => 'float',
        'priceOneWayAdult' => 'float',
        'priceOneWayChild' => 'float',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = BoatTypeParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class, 'boatId');
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('boatId') && $request->boatId) {
                $query->where('boatId', $request->boatId);
            }

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('name', 'LIKE', "%$request->search%");
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('id', 'ASC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function discountedPrice(float $price, int $discountPercentage): float
    {
        if ($discountPercentage <= 0) {
            return $price;
        }

        return round($price - ($price * $discountPercentage / 100), 2);
    }
}