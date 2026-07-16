<?php

namespace App\Http\Requests\Amenity;

use Logia\Core\Validation\Support\FormRequest;

class AmenityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'categoryId' => 'nullable|integer|exists:amenity_categories,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'isPopular' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'isPublish' => 'required|boolean',
        ];
    }
}