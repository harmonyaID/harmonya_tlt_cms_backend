<?php

namespace App\Http\Requests\Auth;

use Logia\Core\Validation\Support\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'required|string',
            'confirmPassword' => 'required_with:password|same:password|string'
        ];
    }
}
