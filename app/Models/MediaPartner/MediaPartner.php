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
        'date' => 'date',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = MediaPartnerParser::class;

    protected static function booted()
    {
        static::forceDeleted(function (self $mediaPartner) {
            $dirPath = PathConstant::IMAGES_MEDIA_PARTNER_STORAGE_PUBLIC_PATH();

            foreach (['featuredImage', 'logo'] as $column) {
                if ($mediaPartner->{$column} && is_file($dirPath . $mediaPartner->{$column})) {
                    @unlink($dirPath . $mediaPartner->{$column});
                }
            }
        });
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
                    $search->where('title', 'LIKE', "%$request->search%")
                        ->orWhere('name', 'LIKE', "%$request->search%")
                        ->orWhere('description', 'LIKE', "%$request->search%");
                });
            }

        })->orderBy('id', 'DESC');
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

        return Storage::disk('public')->url(PathConstant::IMAGES_MEDIA_PARTNER . $this->featuredImage);
    }

    public function logoUrl()
    {
        if (!$this->logo) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_MEDIA_PARTNER . $this->logo);
    }
}