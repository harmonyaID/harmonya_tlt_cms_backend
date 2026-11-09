<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyContactFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'propertyId' => 'required|integer|exists:properties,id',
            'sourceTypeId' => 'nullable|integer',

            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'phone' => 'nullable|string',
        ];
    }
}
