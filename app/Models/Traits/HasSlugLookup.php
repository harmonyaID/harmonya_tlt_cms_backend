<?php

namespace App\Models\Traits;

trait HasSlugLookup
{
    /**
     * Match a record by its slug OR its numeric id, so the public API can
     * accept either a pretty URL slug (SEO-friendly) or a raw id.
     *
     * Usage: Blog::where('isActive', true)->bySlugOrId($value)->first();
     *
     * @param $query
     * @param string|int $value
     *
     * @return mixed
     */
    public function scopeBySlugOrId($query, $idOrSlug)
    {
        return $query->where(function ($query) use ($idOrSlug) {
            $query->where('id', $idOrSlug)
                ->orWhereHas('seo', function ($seo) use ($idOrSlug) {
                    $seo->where('slug', $idOrSlug);
                });
        });
    }
}
