<?php

namespace App\Http\Requests\TltTestimonial;

use Logia\Core\Validation\Support\FormRequest;

class TltTestimonialRequest extends FormRequest
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
            'position' => 'nullable|string',
            'company' => 'nullable|string',
            'testimonial' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'isActive' => 'required|boolean',

            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deletePhoto' => 'nullable|boolean',
        ];
    }
}
