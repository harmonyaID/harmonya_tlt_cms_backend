<?php

namespace App\Http\Requests\TltReview;

use Logia\Core\Validation\Support\FormRequest;

class TltReviewRequest extends FormRequest
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
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'isActive' => 'required|boolean',

            // multiple photo upload, contoh field: photos[]
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            // dipakai khusus saat update, untuk hapus foto tertentu
            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:tlt_review_photos,id',
        ];
    }
}