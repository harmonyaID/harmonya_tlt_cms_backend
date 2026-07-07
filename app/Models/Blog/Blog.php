<?php

namespace App\Models\Blog;

use App\Models\BaseModel;
use App\Parser\Blog\BlogParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Blog extends BaseModel
{
    use SoftDeletes;

    protected $table = 'blogs';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'isActive' => 'boolean',
        'publishedAt' => 'datetime',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = BlogParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'categoryId');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag', 'blogId', 'tagId');
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
                        ->orWhere('excerpt', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('categoryId') && $request->categoryId) {
                $query->where('categoryId', $request->categoryId);
            }

            if ($request->has('isActive') && $request->isActive !== null) {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('id', 'DESC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function thumbnailUrl()
    {
        if (!$this->thumbnail) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_BLOG . $this->thumbnail);
    }
}