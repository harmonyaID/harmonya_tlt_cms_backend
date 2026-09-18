<?php

namespace App\Http\Requests\Seo;

use Illuminate\Validation\Rule;

class SeoRule
{
    public static function rules(
        string $prefix = 'seo.',
        ?int $seoId = null
    ): array {
        return [
            "{$prefix}info" => 'nullable|string|max:255',
            "{$prefix}title" => 'nullable|string|max:255',

            "{$prefix}slug" => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('contentseo', 'slug')
                    ->ignore($seoId),
            ],

            "{$prefix}description" => 'nullable|string',
            "{$prefix}metaKeyword" => 'nullable|string',
            "{$prefix}canonicalUrl" => 'nullable|url|max:255',

            "{$prefix}robotIndex" => 'nullable|boolean',
            "{$prefix}robotFollow" => 'nullable|boolean',

            "{$prefix}schemaMarkup" => 'nullable|array',

            "{$prefix}thumbnail" => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            "{$prefix}deleteThumbnail" => 'nullable|boolean',
        ];
    }
}