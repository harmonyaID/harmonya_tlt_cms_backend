<?php

namespace App\Http\Requests\LembonganArea;

use App\Http\Requests\Seo\SeoRule;
use App\Models\LembonganArea\LembonganArea;
use Logia\Core\Validation\Support\FormRequest;

class LembonganAreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $area = LembonganArea::with('seo')->find($this->route('id'));

        return array_merge([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'additionalContent' => 'nullable|string',
            'url' => 'nullable|string',
            'order' => 'nullable|integer',
            'isActive' => 'required|boolean',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteFeaturedImage' => 'nullable|boolean',

            'seo' => 'nullable|array',
        ], SeoRule::rules('seo.', $area?->seo));
    }
}
