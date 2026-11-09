<?php

namespace App\Http\Requests\Offer;

use Logia\Core\Validation\Support\FormRequest;

class OfferCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
        ];
    }
}
