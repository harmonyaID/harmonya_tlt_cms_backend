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
    public function scopeBySlugOrId($query, $value)
    {
        return $query->where(function ($q) use ($value) {
            $q->where('slug', $value);

            if (is_numeric($value)) {
                $q->orWhere('id', $value);
            }
        });
    }
}
