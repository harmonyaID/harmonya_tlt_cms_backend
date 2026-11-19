<?php

namespace App\Http\Requests\Property;

use App\Models\Property\PropertyVariant;
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

            // Booking quote (Bookeasy booking form), stored as sent by the website
            'propertyVariantId' => 'nullable|integer|exists:property_variants,id',
            'currency' => 'nullable|string|max:10',
            'pricePerNight' => 'nullable|numeric|min:0',
            'nights' => 'nullable|integer|min:1',
            'fee' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'payableAmount' => 'nullable|numeric|min:0',
            'payableCurrency' => 'nullable|string|max:10',
            'rateOptions' => 'nullable|array',
            'rateOptions.*' => 'string|max:100',
            'couponCode' => 'nullable|string|max:100',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->propertyVariantId || !$this->propertyId) {
                return;
            }

            $parentId = PropertyVariant::whereKey($this->propertyVariantId)->value('parentPropertyId');

            if ($parentId && (int) $parentId !== (int) $this->propertyId) {
                $validator->errors()->add('propertyVariantId', 'The selected unit does not belong to this property.');
            }
        });
    }
}
