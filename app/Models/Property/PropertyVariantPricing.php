<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyVariantPricing extends BaseModel
{
    protected $table = 'property_variant_pricings';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $casts = [
        'propertyVariantId' => 'integer',
        'weekdayBasePrice' => 'decimal:2',
        'weekendBasePrice' => 'decimal:2',
        'cleaningFee' => 'decimal:2',
        'cleaningFeeTypeId' => 'integer',
        'extraPersonFee' => 'decimal:2',
        'securityDepositFee' => 'decimal:2',
        'weeklyDiscount' => 'decimal:2',
        'monthlyDiscount' => 'decimal:2',
        'markupPercent' => 'decimal:2',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
    ];

    public function propertyVariant(): BelongsTo
    {
        return $this->belongsTo(PropertyVariant::class, 'propertyVariantId');
    }
}