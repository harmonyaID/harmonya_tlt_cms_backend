<?php

namespace App\Http\Requests\Setting;

use Logia\Core\Validation\Support\FormRequest;

class SettingAmenityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'categoryId' => 'nullable|integer|exists:setting_amenity_categories,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'isPopular' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'isPublish' => 'required|boolean',
        ];
    }
}
