<?php

namespace App\Http\Requests\Experience;

use Logia\Core\Validation\Support\FormRequest;

class ExperienceRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'experienceTypeId' => 'required|integer|exists:experience_types,id',
            'experienceCategoryId' => 'nullable|integer|exists:experience_categories,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'openHours' => 'nullable|string',
            'mapEmbedUrl' => 'nullable|string',
            'mapLocationUrl' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'instagram' => 'nullable|string',
            'website' => 'nullable|string',
            'isActive' => 'required|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'catalogPdf' => 'nullable|file|mimes:pdf|max:10240',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:experience_photos,id',
            'showInquiry' => 'required|boolean',
        ];
    }
}