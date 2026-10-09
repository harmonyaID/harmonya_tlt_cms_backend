<?php

namespace App\Http\Requests\IslandGuide;

use App\Http\Requests\Seo\SeoRule;
use App\Models\IslandGuide\IslandGuideArea;
use Logia\Core\Validation\Support\FormRequest;

class IslandGuideAreaRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $islandGuideAreaId = $this->route('id');
        $islandGuideArea = IslandGuideArea::with('seo')->find($islandGuideAreaId);

        return [
            'islandGuideTypeId' => 'required|integer|exists:island_guide_types,id',
            'name' => 'required|string',
            'description' => 'nullable|string',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteFeaturedImage' => 'nullable|boolean',

            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteBanner' => 'nullable|boolean',

            'seo' => 'nullable|array',
        ] + SeoRule::rules('seo.', $islandGuideArea?->seo);
    }
}
