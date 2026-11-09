<?php

namespace App\Http\Requests\Setting;

use Logia\Core\Validation\Support\FormRequest;

class SettingAmenityCategoryIconRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'icon' => 'required_without:deleteIcon|file|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'deleteIcon' => 'nullable|boolean',
        ];
    }
}
