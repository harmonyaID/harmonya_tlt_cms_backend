<?php

namespace App\Http\Requests\IslandGuide;

use Logia\Core\Validation\Support\FormRequest;

class IslandGuideAreaRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'islandGuideTypeId' => 'required|integer|exists:island_guide_types,id',
            'name' => 'required|string',
            'description' => 'nullable|string',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteFeaturedImage' => 'nullable|boolean',

            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteBanner' => 'nullable|boolean',
        ];
    }
}
