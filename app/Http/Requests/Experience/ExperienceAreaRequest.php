<?php

namespace App\Http\Requests\Experience;

use Logia\Core\Validation\Support\FormRequest;

class ExperienceAreaRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'experienceTypeId' => 'required|integer|exists:experience_types,id',
            'name' => 'required|string',
            'description' => 'nullable|string',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteFeaturedImage' => 'nullable|boolean',

            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteBanner' => 'nullable|boolean',
        ];
    }
}
