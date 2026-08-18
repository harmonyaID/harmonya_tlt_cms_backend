<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyInquiryFormRequest extends FormRequest
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

            // Customer Info
            'name' => 'required|string',
            'email' => 'required|email',
            'countryCode' => 'required|string',
            'mobileNumber' => 'required|string',

            // Dates - either a fixed check-in/check-out, or "my dates are flexible" + month/year
            'isDatesFlexible' => 'required|boolean',
            'checkInDate' => 'required_if:isDatesFlexible,false|nullable|date',
            'checkOutDate' => 'required_if:isDatesFlexible,false|nullable|date|after_or_equal:checkInDate',
            'flexibleMonth' => 'required_if:isDatesFlexible,true|nullable|string',
            'flexibleYear' => 'required_if:isDatesFlexible,true|nullable|integer|min:' . date('Y'),

            // Guests
            'adultCount' => 'required|integer|min:1',
            'childrenAges' => 'nullable|array',
            'childrenAges.*' => 'integer|min:0|max:17',

            'comments' => 'nullable|string',
        ];
    }
}
