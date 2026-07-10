<?php

namespace App\Models\Boat;

use App\Models\BaseModel;
use App\Models\Boat\Boat;
use App\Parser\Boat\BoatContactFormParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoatContactForm extends BaseModel
{
    use SoftDeletes;

    protected $table = 'boat_contact_forms';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'bookedThroughTlt' => 'boolean',
        'hasSurfboard' => 'boolean',
        'isRead' => 'boolean',
        'adultCount' => 'integer',
        'childCount' => 'integer',
        'infantCount' => 'integer',
        'departureDateFromBali' => 'date',
        'departureDateFromLembongan' => 'date',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = BoatContactFormParser::class;

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

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where(function ($search) use ($request) {
                    $search->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('email', 'LIKE', "%$request->search%")
                        ->orWhere('phone', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('boatId') && $request->boatId) {
                $query->where('boatId', $request->boatId);
            }

            if ($request->has('ticketType') && $request->ticketType) {
                $query->where('ticketType', $request->ticketType);
            }

            if ($request->has('isRead') && $request->isRead !== null) {
                $query->where('isRead', $request->isRead);
            }

        })->orderBy('id', 'DESC');
    }
}