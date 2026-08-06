<?php

namespace App\Http\Requests\Acf;

class AcfRule
{
    public static function rules(string $prefix = 'acf'): array
    {
        return [
            "{$prefix}" => 'nullable|array',
            "{$prefix}.*.key" => 'nullable|string|max:255',
            "{$prefix}.*.label" => 'nullable|string|max:255',
            "{$prefix}.*.value" => 'nullable',
        ];
    }
}
