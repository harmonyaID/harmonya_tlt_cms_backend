<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyRelatedRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'relatedPropertyIds' => 'present|array',
            'relatedPropertyIds.*' => 'integer|exists:properties,id',
        ];
    }
}
