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
            'propertyId' => 'nullable|integer|exists:properties,id',
            'sourceTypeId' => 'nullable|integer',

            // Customer info
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',

            // Booking detail
            'checkInDate' => 'nullable|date',
            'checkOutDate' => 'nullable|date|after_or_equal:checkInDate',
            'adultCount' => 'required|integer|min:1',
            'childCount' => 'required|integer|min:0',
            'infantCount' => 'required|integer|min:0',
            'message' => 'nullable|string',
        ];
    }
}
