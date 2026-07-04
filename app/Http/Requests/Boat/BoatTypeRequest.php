<?php

namespace App\Http\Requests\Boat;

use Logia\Core\Validation\Support\FormRequest;

class BoatTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'boatId' => 'required|integer|exists:boats,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'priceReturnAdult' => 'required|numeric|min:0',
            'priceReturnChild' => 'required|numeric|min:0',
            'priceOneWayAdult' => 'required|numeric|min:0',
            'priceOneWayChild' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'childAgeNote' => 'nullable|string',
            'isActive' => 'required|boolean',
        ];
    }
}