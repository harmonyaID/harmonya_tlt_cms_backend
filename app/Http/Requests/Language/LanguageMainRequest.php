<?php

namespace App\Http\Requests\Language;

use Logia\Core\Validation\Support\FormRequest;

class LanguageMainRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['main' => 'required'];
    }
}
