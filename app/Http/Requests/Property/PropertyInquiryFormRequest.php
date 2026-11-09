<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyInquiryFormRequest extends FormRequest
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
            'countryCode' => 'required|string',
            'mobileNumber' => 'required|string',

            'isDatesFlexible' => 'required|boolean',
            'checkInDate' => 'required_if:isDatesFlexible,false|nullable|date',
            'checkOutDate' => 'required_if:isDatesFlexible,false|nullable|date|after_or_equal:checkInDate',
            'flexibleMonth' => 'required_if:isDatesFlexible,true|nullable|string',
            'flexibleYear' => 'required_if:isDatesFlexible,true|nullable|integer|min:' . date('Y'),

            'adultCount' => 'required|integer|min:1',
            'childrenAges' => 'nullable|array',
            'childrenAges.*' => 'integer|min:0|max:17',

            'comments' => 'nullable|string',
        ];
    }
}
