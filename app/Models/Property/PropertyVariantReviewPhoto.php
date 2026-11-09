<?php

namespace App\Models\Property;

use App\Models\BaseModel;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyVariantReviewPhoto extends BaseModel
{
    protected $table = 'property_variant_review_photos';

    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public $timestamps = true;

    public function review(): BelongsTo
    {
        return $this->belongsTo(PropertyVariantReview::class, 'reviewId');
    }

    public function photoUrl()
    {
        return Storage::disk('public')->url(PathConstant::IMAGES_PROPERTY_REVIEW . $this->photo);
    }
}
