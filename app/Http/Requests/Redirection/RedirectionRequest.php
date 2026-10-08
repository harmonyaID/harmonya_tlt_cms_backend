<?php

namespace App\Http\Requests\Redirection;

use Illuminate\Validation\Rule;
use Logia\Core\Validation\Support\FormRequest;

class RedirectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $redirectionId = $this->route('id');

        return [
            'name' => 'required|string|max:255',

            'sourceUrl' => [
                'required',
                'string',
                'max:2048',
                Rule::unique('redirections', 'sourceUrl')
                    ->ignore($redirectionId),
            ],

            'targetUrl' => 'required|string|max:2048',

            'statusCode' => [
                'required',
                'integer',
                Rule::in([301, 302]),
            ],

            'isActive' => 'required|boolean',
        ];
    }
}