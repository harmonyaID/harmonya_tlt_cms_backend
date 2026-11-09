<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyGuestyConfigurationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clientId' => 'nullable|string',
            'clientSecret' => 'nullable|string',
            'authUrl' => 'nullable|url',
            'baseUrl' => 'nullable|url',
            'isActive' => 'required|boolean',
        ];
    }
}
