<?php

namespace App\Http\Requests\TltTestimonial;

use Logia\Core\Validation\Support\FormRequest;

class TltTestimonialRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

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
