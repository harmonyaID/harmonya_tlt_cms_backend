<?php

namespace App\Http\Requests\TltReview;

use Logia\Core\Validation\Support\FormRequest;

class TltReviewRequest extends FormRequest
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
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'isActive' => 'required|boolean',

            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:tlt_review_photos,id',
        ];
    }
}
