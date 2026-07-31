<?php

namespace App\Http\Requests\Setting;

use Logia\Core\Validation\Support\FormRequest;

class ApiConfigurationRequest extends FormRequest
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
            'name' => 'required|string',
            'module' => 'nullable|string',
            'isActive' => 'required|boolean',

            // free-form key-value credential set, e.g. {"client_id": "...", "client_secret": "..."}
            'credentials' => 'nullable|array',
            'credentials.*' => 'nullable|string',
        ];
    }
}
