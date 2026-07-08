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
            'openHours' => 'nullable|string',
            'description' => 'nullable|string',
            'mapLocationUrl' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'instagram' => 'nullable|string',
            'website' => 'nullable|string',
            'isActive' => 'required|boolean',
            'showInquiry' => 'required|boolean',

            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'mapImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:experience_photos,id',

            'catalogs' => 'nullable|array',
            'catalogs.*.name' => 'required_with:catalogs|string',
            'catalogs.*.file' => 'nullable|file|mimes:pdf|max:10240',
            'catalogs.*.existingFile' => 'nullable|string',
        ];
    }
}