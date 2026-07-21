<?php

namespace App\Models\SEO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentSeo extends Model
{
    use SoftDeletes;

    protected $table = 'contentseo';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    
    protected $fillable = [
        'contentableId',
        'contentableType',
        'info',
        'title',
        'slug',
        'description',
        'metaKeyword',
        'thumbnail',
        'canonicalUrl',
        'robotIndex',
        'robotFollow',
        'structuredData',
    ];

    protected $casts = [
        'robotIndex' => 'boolean',
        'robotFollow' => 'boolean',
        'structuredData' => 'array',
    ];

    public function contentable()
    {
        return $this->morphTo();
    }
    public function thumbnailUrl()
    {
        if (!$this->thumbnail) {
            return null;
        }

        return asset('storage/images/seo/' . $this->thumbnail);
    }
}
