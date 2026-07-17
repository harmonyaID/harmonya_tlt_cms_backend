<?php

namespace App\Http\Requests\Property;

use Logia\Core\Validation\Support\FormRequest;

class PropertyReviewRequest extends FormRequest
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
            'propertyId' => 'required|integer|exists:properties,id',
            'name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'isActive' => 'required|boolean',

            // multiple photo upload, field: photos[]
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            // dipakai khusus saat update, untuk hapus foto tertentu
            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:property_review_photos,id',
        ];
    }
}
