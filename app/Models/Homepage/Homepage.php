<?php

namespace App\Models\Homepage;

use App\Models\BaseModel;
use App\Models\SEO\ContentSeo;
use App\Parser\Homepage\HomepageParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Homepage extends BaseModel
{
    use SoftDeletes;

    protected $table = 'homepages';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'value' => 'array',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = HomepageParser::class;

    // Keys within `value` that hold an uploaded file (image/video) name
    public static array $imageKeys = [
        'image',
        'images',
        'thumbnail',
        'video',
        'backgroundVideo',
        'videoThumbnail',
        'mapImage',
        'background',
        'backgroundImage',
        'logo',
        'icon',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function seo()
    {
        return $this->morphOne(ContentSeo::class, 'contentable', 'contentableType', 'contentableId');
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('locale') && $request->locale) {
                $query->where('locale', $request->locale);
            }
        })->orderBy('id', 'DESC');
    }

    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function resolvedValue(): array
    {
        return $this->resolveImages($this->value ?? []);
    }

    public function imageUrl(?string $filename)
    {
        if (!$filename) {
            return null;
        }

        if (Str::startsWith($filename, ['http://', 'https://'])) {
            return $filename;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_HOMEPAGE . $filename);
    }

    private function resolveImages($data, $key = null)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $k => $v) {
                $result[$k] = $this->resolveImages($v, is_int($k) ? $key : $k);
            }
            return $result;
        }

        if (is_string($data) && $data !== '' && $key && in_array($key, self::$imageKeys, true)) {
            return $this->imageUrl($data);
        }

        return $data;
    }
}

