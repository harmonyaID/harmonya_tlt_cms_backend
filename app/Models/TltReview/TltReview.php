<?php

namespace App\Models\TltReview;

use App\Models\BaseModel;
use App\Parser\TltReview\TltReviewParser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TltReview extends BaseModel
{
    use SoftDeletes;

    protected $table = 'tlt_reviews';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $casts = [
        'rating' => 'integer',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = TltReviewParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function photos(): HasMany
    {
        return $this->hasMany(TltReviewPhoto::class, 'reviewId');
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
                        ->orWhere('company', 'LIKE', "%$request->search%")
                        ->orWhere('review', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('id', 'DESC');
    }
}