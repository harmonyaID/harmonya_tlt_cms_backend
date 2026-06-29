<?php

namespace App\Models\MediaPartner;

use App\Models\BaseModel;
use App\Parser\MediaPartner\MediaPartnerParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MediaPartner extends BaseModel
{
    use SoftDeletes;

    protected $table = 'media_partners';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $casts = [
        'isPublish' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = MediaPartnerParser::class;

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('description', 'LIKE', "%$request->search%");
            }

        })->orderBy('id', 'DESC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function imageUrl()
    {
        if (!$this->image) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_MEDIA_PARTNER . $this->image);
    }
}