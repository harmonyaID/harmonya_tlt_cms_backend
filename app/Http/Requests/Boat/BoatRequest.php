<?php

namespace App\Http\Requests\Boat;

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
            'name' => 'required|string',
            'description' => 'nullable|string',
            'routeFrom' => 'nullable|string',
            'routeTo' => 'nullable|string',
            'departureTimesFromBali' => 'nullable|array',
            'departureTimesFromBali.*' => 'string',
            'departureTimesFromLembongan' => 'nullable|array',
            'departureTimesFromLembongan.*' => 'string',
            'notes' => 'nullable|string',
            'discountPercentage' => 'nullable|integer|min:0|max:100',
            'isActive' => 'required|boolean',

            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'deletePhotoIds' => 'nullable|array',
            'deletePhotoIds.*' => 'integer|exists:boat_photos,id',
        ];
    }
}