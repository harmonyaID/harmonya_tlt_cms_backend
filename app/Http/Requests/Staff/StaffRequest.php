<?php

namespace App\Http\Requests\Staff;

use Logia\Core\Validation\Support\FormRequest;

class StaffRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fullName' => 'required|string',
            'email' => 'required|string',
            'phone' => 'string|nullable',
            'genderId' => 'required|integer',
            'countryId' => 'integer|nullable',
            'address' => 'required|string',
            'isActive' => 'required|boolean',
            'isSuperadmin' => 'required|boolean',
            'password' => 'string|nullable',
            'confirmPassword' => 'required_with:password|same:password|string|nullable'
        ];
    }
}
