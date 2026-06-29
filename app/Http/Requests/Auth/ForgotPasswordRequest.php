<?php

namespace App\Http\Requests\Auth;

use Logia\Core\Validation\Support\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|string'
        ];
    }
}