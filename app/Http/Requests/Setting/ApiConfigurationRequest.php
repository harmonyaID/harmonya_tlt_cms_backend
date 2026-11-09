<?php

namespace App\Http\Requests\Setting;

use Logia\Core\Validation\Support\FormRequest;

class ApiConfigurationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'module' => 'nullable|string',
            'isActive' => 'required|boolean',

            'credentials' => 'nullable|array',
            'credentials.*' => 'nullable|string',
        ];
    }
}
