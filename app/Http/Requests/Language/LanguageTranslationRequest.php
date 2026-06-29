<?php

namespace App\Http\Requests\Language;

use Logia\Core\Validation\Support\FormRequest;

class LanguageTranslationRequest extends FormRequest
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
            'groupId' => 'integer|nullable',
            'key' => 'required|string',
            'translations' => 'required|array|min:1'
        ];
    }
}
