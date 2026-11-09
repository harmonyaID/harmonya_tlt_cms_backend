<?php

namespace App\Http\Requests\Language;

use Logia\Core\Validation\Support\FormRequest;

class LanguageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string',
            'country' => 'required|string',
            'main' => 'required',
        ];
    }
}
