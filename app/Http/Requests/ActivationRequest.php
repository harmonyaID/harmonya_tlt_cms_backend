<?php

namespace App\Http\Requests;

use Logia\Core\Validation\Support\FormRequest;

class ActivationRequest extends FormRequest
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
            'isActive' => 'required|boolean'
        ];
    }
}
