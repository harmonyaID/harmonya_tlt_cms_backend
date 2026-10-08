<?php

namespace App\Models\Traits;

trait HasSeoSlugScope
{
    public function scopeBySlugOrId($query, $value)
    {
        return $query->where(function ($q) use ($value) {
            $q->whereHas('seo', function ($seo) use ($value) {
                $seo->where('slug', $value);
            });

            if (is_numeric($value)) {
                $q->orWhere('id', $value);
            }
        });
    }
}