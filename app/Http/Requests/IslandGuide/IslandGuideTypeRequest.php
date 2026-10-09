<?php

namespace App\Http\Requests\IslandGuide;

use App\Http\Requests\Seo\SeoRule;
use App\Models\IslandGuide\IslandGuideType;
use Logia\Core\Validation\Support\FormRequest;

class IslandGuideTypeRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $islandGuideTypeId = $this->route('id');
        $islandGuideType = IslandGuideType::with('seo')->find($islandGuideTypeId);

        return [
            'name' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'description' => 'nullable|string',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteFeaturedImage' => 'nullable|boolean',

            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteBanner' => 'nullable|boolean',

            'isPage' => 'nullable|boolean',
            'pageId' => 'nullable|integer|exists:pages,id',

            'seo' => 'nullable|array',
        ] + SeoRule::rules('seo.', $islandGuideType?->seo);
    }
}
