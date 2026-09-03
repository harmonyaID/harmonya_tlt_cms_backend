<?php

namespace App\Http\Requests\Auth;

use Logia\Core\Validation\Support\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'currentPassword'  => 'required|string',
            'password'         => 'required|string|min:8',
            'confirmPassword'  => 'required_with:password|same:password|string'
        ];
    }
}