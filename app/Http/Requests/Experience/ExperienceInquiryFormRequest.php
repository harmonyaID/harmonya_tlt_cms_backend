<?php

namespace App\Http\Requests\Experience;

use Logia\Core\Validation\Support\FormRequest;

class ExperienceInquiryFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'experienceId'        => 'required|integer|exists:experiences,id',
            'fullName'            => 'required|string',
            'phone'               => 'required|string',
            'email'               => 'required|email',
            'eventDate'           => 'nullable|date',
            'totalGuests'         => 'nullable|integer|min:1',
            'countryOfResidence'  => 'nullable|string',
            'mealStyle'           => 'nullable|string',
            'weddingLocation'     => 'nullable|string',
            'ceremonyType'        => 'nullable|string',
            'accommodationNights' => 'nullable|integer|min:0',
            'maxNightlyBudget'    => 'nullable|integer|min:0',
            'notes'               => 'nullable|string',
        ];
    }
}