<?php

namespace App\Http\Requests\LembonganArea;

use Logia\Core\Validation\Support\FormRequest;

class LembonganAreaRequest extends FormRequest
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
            'content' => 'nullable|string',
            'url' => 'nullable|string',
            'order' => 'nullable|integer',
            'isActive' => 'required|boolean',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteFeaturedImage' => 'nullable|boolean',
        ];
    }
}
