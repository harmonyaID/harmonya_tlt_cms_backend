<?php

namespace App\Models\Boat;

use App\Models\BaseModel;
use App\Models\SEO\ContentSeo;
use App\Models\Traits\HasDateRangeFilter;
use App\Parser\Boat\BoatParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boat extends BaseModel
{
    use SoftDeletes;
    use HasDateRangeFilter;

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

    public function photos(): HasMany
    {
        return $this->hasMany(BoatPhoto::class, 'boatId')->orderBy('order');
    }

    public function customInformations(): HasMany
    {
        return $this->hasMany(BoatCustomInformation::class, 'boatId')->orderBy('order');
    }

    public function seo()
    {
        return $this->morphOne(ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }
    
    public function type(): BelongsTo
    {
        return $this->belongsTo(BoatComponentType::class, 'boatComponentTypeId');
    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where(function ($search) use ($request) {
                    $search->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('description', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('boatComponentTypeId') && $request->boatComponentTypeId) {
                $query->where('boatComponentTypeId', $request->boatComponentTypeId);
            }

            if ($request->has('locale') && $request->locale) {
                $query->where('locale', $request->locale);
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

            $this->applyDateRangeFilter($query, $request);
        })->orderBy('id', 'DESC');
    }
}