<?php

namespace App\Http\Requests\Boat;

use App\Http\Requests\Acf\AcfRule;
use App\Http\Requests\Seo\SeoRule;
use Illuminate\Validation\Rule;
use Logia\Core\Validation\Support\FormRequest;

class BoatRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'boatComponentTypeId'  => [
                'required',
                'integer',
                Rule::exists('boat_component_types', 'id')->whereNull('deletedAt'),
            ],
            'name'                 => 'required|string',
            'description'          => 'nullable|string',
            'isActive'             => 'required|boolean',
            'locale'               => 'nullable|string|exists:languages,code',
            'promoLabel'           => 'nullable|string|max:255',

            'priceFile'            => 'nullable|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
            'deletePriceFile'      => 'nullable|boolean',

            'promoPhotos'          => 'nullable|array',
            'promoPhotos.*'        => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'deletePromoPhotoIds'  => 'nullable|array',
            'deletePromoPhotoIds.*' => 'integer',

            'photos'               => 'nullable|array',
            'photos.*'             => 'image|mimes:jpg,jpeg,png,webp|max:5120',

            'deletePhotoIds'       => 'nullable|array',
            'deletePhotoIds.*'     => 'integer|exists:boat_photos,id',

            // custom informations grouped by name
            'customInformations' => 'nullable|array',
            'customInformations.*.name' => 'required|string',
            'customInformations.*.customInformations' => 'required|array',
            'customInformations.*.customInformations.*.id' => 'nullable|integer',
            'customInformations.*.customInformations.*.name' => 'required|string',
            'customInformations.*.customInformations.*.value' => 'required|string',
            'customInformations.*.customInformations.*.order' => 'nullable|integer',

            'seo' => 'nullable|array',
        ] + SeoRule::rules() + AcfRule::rules();
    }
}
