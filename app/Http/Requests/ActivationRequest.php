<?php

namespace App\Http\Requests;

use Logia\Core\Validation\Support\FormRequest;

class ActivationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'isActive' => 'required|boolean'
        ];
    }
}
