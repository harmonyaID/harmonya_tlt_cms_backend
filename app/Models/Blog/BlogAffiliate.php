<?php

namespace App\Models\Blog;

use App\Models\BaseModel;
use App\Parser\Blog\BlogAffiliateParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Affiliate banner shown on blogs. isAppliedToAll = true: on every blog,
 * false: only on the blogs it is assigned to.
 */
class BlogAffiliate extends BaseModel
{
    use SoftDeletes;

    protected $table = 'blog_affiliates';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'isAppliedToAll' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = BlogAffiliateParser::class;

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_affiliate', 'affiliateId', 'blogId');
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
                $query->where('title', 'LIKE', "%$request->search%");
            }

            // isAppliedToAll=0 -> the affiliates that can be assigned to a blog
            if ($request->has('isAppliedToAll') && $request->isAppliedToAll !== null && $request->isAppliedToAll !== '') {
                $query->where('isAppliedToAll', filter_var($request->isAppliedToAll, FILTER_VALIDATE_BOOLEAN));
            }

        })->orderBy('id', 'DESC');
    }

    public function scopeAppliedToAll($query)
    {
        return $query->where('isAppliedToAll', true);
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function bannerImageUrl(): ?string
    {
        if (!$this->bannerImage) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_BLOG_AFFILIATE . $this->bannerImage);
    }

    /**
     * Affiliates with isAppliedToAll = true, loaded once per request.
     */
    public static function appliedToAllCached()
    {
        $attributes = request()->attributes;

        if (!$attributes->has('blogAffiliatesAppliedToAll')) {
            $attributes->set('blogAffiliatesAppliedToAll', static::appliedToAll()->orderBy('id')->get());
        }

        return $attributes->get('blogAffiliatesAppliedToAll');
    }
}
