<?php

namespace App\Http\Requests\Setting;

use Logia\Core\Validation\Support\FormRequest;

class SettingPropertyFeatureRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'hasValue' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'deleteIcon' => 'nullable|boolean',
        ];
    }
}
