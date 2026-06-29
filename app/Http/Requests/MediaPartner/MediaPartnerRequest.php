<?php

namespace App\Http\Requests\MediaPartner;

use Logia\Core\Validation\Support\FormRequest;

class MediaPartnerRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'nullable|string',
            'url' => 'nullable|string|url',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'isPublish' => 'required|boolean',

        ];
    }
}