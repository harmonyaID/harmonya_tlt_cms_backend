<?php

namespace App\Http\Requests\Visitor;

use Illuminate\Validation\Rule;
use Logia\Core\Validation\Support\FormRequest;

class TrackVisitorRequest extends FormRequest
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
            'contentType' => 'required|string|in:blog',
            'contentId' => 'required|integer',
        ];
    }
}
