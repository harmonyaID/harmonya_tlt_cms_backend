<?php

namespace App\Http\Requests\Visitor;

use Logia\Core\Validation\Support\FormRequest;

class TrackVisitorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contentType' => 'required|string|in:blog',
            'contentId' => 'required|integer',
        ];
    }
}
