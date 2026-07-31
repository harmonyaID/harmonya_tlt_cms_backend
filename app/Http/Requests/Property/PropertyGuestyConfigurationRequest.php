<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyGuestyConfigurationRequest extends FormRequest
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
            'clientId' => 'nullable|string',
            'clientSecret' => 'nullable|string',
            'authUrl' => 'nullable|url',
            'baseUrl' => 'nullable|url',
            'isActive' => 'required|boolean',
        ];
    }
}
