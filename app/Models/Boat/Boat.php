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
        'boatComponentTypeId' => 'integer',
        'promoPhotos'         => 'array',
        'isActive'            => 'boolean',
        self::CREATED_AT      => 'datetime',
        self::UPDATED_AT      => 'datetime',
        self::DELETED_AT      => 'datetime',
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

    public function customInformations(): HasMany
    {
        return $this->hasMany(BoatCustomInformation::class, 'boatId')->orderBy('order');
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
                $query->where('description', 'LIKE', "%$request->search%");
            }

            if ($request->has('boatComponentTypeId') && $request->boatComponentTypeId) {
                $query->where('boatComponentTypeId', $request->boatComponentTypeId);
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('id', 'DESC');
    }
}