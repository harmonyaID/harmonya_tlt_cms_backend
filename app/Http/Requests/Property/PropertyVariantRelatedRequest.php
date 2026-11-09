<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyVariantRelatedRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'relatedPropertyVariantIds' => 'present|array',
            'relatedPropertyVariantIds.*' => 'integer|exists:property_variants,id',
        ];
    }
}
