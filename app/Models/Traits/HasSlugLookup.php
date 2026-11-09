<?php

namespace App\Models\Traits;

trait HasSlugLookup
{
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
