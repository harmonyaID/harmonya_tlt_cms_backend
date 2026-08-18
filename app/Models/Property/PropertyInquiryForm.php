<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use App\Parser\Property\PropertyInquiryFormParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyInquiryForm extends BaseModel
{
    use SoftDeletes;

    protected $table = 'property_inquiry_forms';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'statusId' => 'integer',
        'sourceTypeId' => 'integer',
        'isRead' => 'boolean',
        'isDatesFlexible' => 'boolean',
        'flexibleYear' => 'integer',
        'adultCount' => 'integer',
        'childrenAges' => 'array',
        'checkInDate' => 'date',
        'checkOutDate' => 'date',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = PropertyInquiryFormParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'propertyId');
    }

    public function sourceType(): BelongsTo
    {
        return $this->belongsTo(PropertySourceType::class, 'sourceTypeId');
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
                        ->orWhere('mobileNumber', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('propertyId') && $request->propertyId) {
                $query->where('propertyId', $request->propertyId);
            }

            if ($request->has('sourceTypeId') && $request->sourceTypeId) {
                $query->where('sourceTypeId', $request->sourceTypeId);
            }

            if ($request->has('isRead') && $request->isRead !== null) {
                $query->where('isRead', $request->isRead);
            }

        })->orderBy('id', 'DESC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function childCount(): int
    {
        return count($this->childrenAges ?? []);
    }
}
