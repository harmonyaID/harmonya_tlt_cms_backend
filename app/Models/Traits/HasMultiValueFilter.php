<?php

namespace App\Models\Traits;

trait HasMultiValueFilter
{
    /**
     * Normalize a filter value that may arrive as a real array (?tagIds[]=1&tagIds[]=2)
     * or as a comma-separated string (?tagIds=1,2) into a clean array of values.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function toValueArray($value): array
    {
        if (is_array($value)) {
            $values = $value;
        } else {
            $values = explode(',', (string)$value);
        }

        return collect($values)
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '')
            ->values()
            ->all();
    }
}
