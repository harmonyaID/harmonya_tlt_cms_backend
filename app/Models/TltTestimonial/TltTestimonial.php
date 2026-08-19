<?php

namespace App\Models\TltTestimonial;

use App\Models\BaseModel;
use App\Parser\TltTestimonial\TltTestimonialParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TltTestimonial extends BaseModel
{
    use SoftDeletes;

    protected $table = 'tlt_testimonials';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'order' => 'integer',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = TltTestimonialParser::class;

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
                        ->orWhere('testimonial', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('isActive') && $request->isActive !== null && $request->isActive !== '') {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('order', 'ASC')->orderBy('id', 'DESC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function photoUrl()
    {
        if (!$this->photo) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_TLT_TESTIMONIAL . $this->photo);
    }
}
