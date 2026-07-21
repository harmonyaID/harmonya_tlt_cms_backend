<?php

namespace App\Models\LembonganArea;

use App\Models\BaseModel;
use App\Parser\LembonganArea\LembonganAreaParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LembonganArea extends BaseModel
{
    use SoftDeletes;

    protected $table = 'lembongan_areas';
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

    public $parserClass = LembonganAreaParser::class;

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

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function featuredImageUrl()
    {
        if (!$this->featuredImage) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_LEMBONGAN_AREA . $this->featuredImage);
    }
}
