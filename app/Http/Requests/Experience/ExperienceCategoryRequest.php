<?php

namespace App\Http\Requests\Experience;

use Logia\Core\Validation\Support\FormRequest;

class ExperienceCategoryRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'experienceTypeId' => 'required|integer|exists:experience_types,id',
            'name' => 'required|string',
        ];
    }
}