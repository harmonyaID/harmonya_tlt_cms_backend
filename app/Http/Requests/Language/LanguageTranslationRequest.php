<?php

namespace App\Http\Requests\Language;

use Logia\Core\Validation\Support\FormRequest;

class LanguageTranslationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'groupId' => 'integer|nullable',
            'key' => 'required|string',
            'translations' => 'required|array|min:1'
        ];
    }
}
