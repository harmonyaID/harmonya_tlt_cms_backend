<?php

namespace App\Http\Requests\Staff;

use Logia\Core\Validation\Support\FormRequest;

class StaffRequest extends FormRequest
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
