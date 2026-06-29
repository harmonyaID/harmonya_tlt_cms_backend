<?php

namespace App\Http\Requests\Faq;

use Logia\Core\Validation\Support\FormRequest;

class FaqRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'question' => 'required|string',
            'answer'   => 'required|string',
            'order'    => 'nullable|integer|min:0',
            'isActive' => 'required|boolean',
        ];
    }
}