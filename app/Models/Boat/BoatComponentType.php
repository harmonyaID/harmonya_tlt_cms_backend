<?php

namespace App\Models\Boat;

use App\Models\BaseModel;
use App\Parser\Boat\BoatComponentTypeParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoatComponentType extends BaseModel
{
    use SoftDeletes;

    protected $table = 'boat_component_types';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = BoatComponentTypeParser::class;

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

        })->orderBy('id', 'DESC');
    }
}