<?php

namespace App\Http\Requests\Boat;

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
            'description'          => 'nullable|string',
            'isActive'             => 'required|boolean',

            // price file
            'priceFile'            => 'nullable|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
            'deletePriceFile'  => 'nullable|boolean',

            // promo photos (json) — kirim ulang semua saat ganti
            'promoPhotos'          => 'nullable|array',
            'promoPhotos.*'        => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'deletePromoPhotoIds'   => 'nullable|array',
            'deletePromoPhotoIds.*' => 'integer',

            // photos
            'photos'               => 'nullable|array',
            'photos.*'             => 'image|mimes:jpg,jpeg,png,webp|max:5120',

            // hapus foto tertentu saat update
            'deletePhotoIds'       => 'nullable|array',
            'deletePhotoIds.*'     => 'integer|exists:boat_photos,id',

            // custom informations
            'customInformations'          => 'nullable|array',
            'customInformations.*.id'     => 'nullable|integer',
            'customInformations.*.name'   => 'required_with:customInformations|string',
            'customInformations.*.value'  => 'required_with:customInformations|string',
            'customInformations.*.order'  => 'nullable|integer',
        ];
    }
}
