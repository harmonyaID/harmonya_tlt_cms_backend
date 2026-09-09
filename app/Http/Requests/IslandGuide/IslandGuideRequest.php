<?php

namespace App\Http\Requests\IslandGuide;

use App\Http\Requests\Acf\AcfRule;
use App\Http\Requests\Seo\SeoRule;
use Logia\Core\Validation\Support\FormRequest;

class IslandGuideRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'islandGuideTypeId' => 'required|integer|exists:island_guide_types,id',
            'islandGuideAreaId' => 'nullable|integer|exists:island_guide_areas,id',
            'name' => 'required|string',
            'openHours' => 'nullable|string',
            'description' => 'nullable|string',
            'mapLocationUrl' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'instagram' => 'nullable|string',
            'website' => 'nullable|string',
            'isActive' => 'required|boolean',
            'locale' => 'nullable|string|exists:languages,code',
            'showInquiry' => 'required|boolean',

            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'mapImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:island_guide_photos,id',

            'catalogs' => 'nullable|array',
            'catalogs.*.id' => 'nullable|integer',
            'catalogs.*.name' => 'required_with:catalogs|string',
            'catalogs.*.file' => 'nullable|file|mimes:pdf|max:10240',

            'deleteCatalogIds' => 'nullable|array',
            'deleteCatalogIds.*' => 'integer',

            'seo' => 'nullable|array',
        ] + SeoRule::rules() + AcfRule::rules();
    }
}
