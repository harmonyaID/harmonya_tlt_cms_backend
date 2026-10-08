<?php

namespace App\Http\Requests\Experience;

use App\Http\Requests\Acf\AcfRule;
use App\Http\Requests\Seo\SeoRule;
use App\Models\Experience\Experience;
use Logia\Core\Validation\Support\FormRequest;

class ExperienceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $experienceId = $this->route('id');
        $experience = Experience::with('seo')->find($experienceId);

        return [
            'experienceTypeId' => 'required|integer|exists:experience_types,id',
            'experienceAreaId' => 'nullable|integer|exists:experience_areas,id',
            'name' => 'required|string',
            'excerpt' => 'nullable|string',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'openHours' => 'nullable|string',
            'mapLocationUrl' => 'nullable|string',

            'contactInfo' => 'nullable|array',
            'contactInfo.whatsapp' => 'nullable|string',
            'contactInfo.email' => 'nullable|email',

            'instagram' => 'nullable|string',
            'website' => 'nullable|string',

            'isActive' => 'required|boolean',
            'locale' => 'nullable|string|exists:languages,code',
            'showInquiry' => 'required|boolean',

            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:experience_photos,id',

            'tagIds' => 'nullable|array',
            'tagIds.*' => 'integer|exists:experience_tags,id',

            'catalogs' => 'nullable|array',
            'catalogs.*.id' => 'nullable|integer',
            'catalogs.*.name' => 'required_with:catalogs|string',
            'catalogs.*.file' => 'nullable|file|mimes:pdf|max:10240',

            'deleteCatalogIds' => 'nullable|array',
            'deleteCatalogIds.*' => 'integer',

            'seo' => 'nullable|array',
        ] + SeoRule::rules('seo.', $experience?->seo)
          + AcfRule::rules();
    }
}