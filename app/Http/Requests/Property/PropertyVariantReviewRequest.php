<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyVariantReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'propertyVariantId' => 'required|integer|exists:property_variants,id',
            'name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'isActive' => 'required|boolean',

            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:property_variant_review_photos,id',
        ];
    }
}
