<?php

namespace App\Models\TltReview;

use App\Models\BaseModel;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TltReviewPhoto extends BaseModel
{
    protected $table = 'tlt_review_photos';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public $timestamps = true;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function review(): BelongsTo
    {
        return $this->belongsTo(TltReview::class, 'reviewId');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function photoUrl()
    {
        return Storage::disk('public')->url(PathConstant::IMAGES_TLT_REVIEW . $this->photo);
    }
}