<?php

namespace App\Http\Requests\Experience;

use Logia\Core\Validation\Support\FormRequest;

class ExperienceAreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'experienceTypeId' => 'required|integer|exists:experience_types,id',
            'name' => 'required|string',
            'description' => 'nullable|string',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteFeaturedImage' => 'nullable|boolean',
            'mapImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteMapImage' => 'nullable|boolean',

            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteBanner' => 'nullable|boolean',

            'customInformations' => 'nullable|array',
            'customInformations.*.name' => 'required|string',
            'customInformations.*.customInformations' => 'required|array',
            'customInformations.*.customInformations.*.id' => 'nullable|integer',
            'customInformations.*.customInformations.*.name' => 'required|string',
            'customInformations.*.customInformations.*.value' => 'required|string',
            'customInformations.*.customInformations.*.order' => 'nullable|integer',

            'experienceSection1Ids' => 'nullable|array',
            'experienceSection1Ids.*' => 'integer|exists:experiences,id',

            'experienceSection2Ids' => 'nullable|array',
            'experienceSection2Ids.*' => 'integer|exists:experiences,id',

            'propertyIds' => 'nullable|array',
            'propertyIds.*' => 'integer|exists:properties,id',

            'blogIds' => 'nullable|array',
            'blogIds.*' => 'integer|exists:blogs,id',
        ];
    }
}
