<?php

namespace App\Http\Requests\Language;

use Logia\Core\Validation\Support\FormRequest;

class LanguageGroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['path' => 'required|string'];
    }
}
