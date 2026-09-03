<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyContactFormRequest extends FormRequest
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
            'propertyId' => 'required|integer|exists:properties,id',
            'sourceTypeId' => 'nullable|integer',

            // Form fields (matches the "INQUIRY" form on the property detail page)
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'phone' => 'nullable|string',
        ];
    }
}
