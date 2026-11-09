<?php

namespace App\Models\Traits;

trait HasMultiValueFilter
{
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
