<?php

namespace App\Http\Requests\Experience;

use Logia\Core\Validation\Support\FormRequest;

class ExperienceTypeRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteFeaturedImage' => 'nullable|boolean',

            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteBanner' => 'nullable|boolean',
        ];
    }
}
