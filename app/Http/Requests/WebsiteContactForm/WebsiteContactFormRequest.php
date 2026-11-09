<?php

namespace App\Http\Requests\WebsiteContactForm;

use Logia\Core\Validation\Support\FormRequest;

class WebsiteContactFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'formTypeId' => 'nullable|integer|exists:component_contact_form_types,id',
            'formInquiryTypeId' => 'nullable|integer|exists:component_contact_form_inquiry_types,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'subject' => 'nullable|string',
            'message' => 'required|string',
        ];
    }
}
