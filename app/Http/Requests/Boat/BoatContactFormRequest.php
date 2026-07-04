<?php

namespace App\Http\Requests\Boat;

use Logia\Core\Validation\Support\FormRequest;

class BoatContactFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'boatId' => 'nullable|integer|exists:boats,id',
            'boatTypeId' => 'nullable|integer|exists:boat_types,id',

            // Customer info
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',

            // Booking detail
            'ticketType' => 'required|in:one_way,return',
            'baliLandLocation' => 'nullable|string',
            'bookedThroughTlt' => 'required|boolean',
            'tltBookingRefName' => 'nullable|string',
            'adultCount' => 'required|integer|min:1',
            'childCount' => 'required|integer|min:0',
            'infantCount' => 'required|integer|min:0',

            // From Bali
            'departureDateFromBali' => 'nullable|date',
            'departureTimeFromBali' => 'nullable|string',
            'pickUpLocationBali' => 'nullable|string',
            'flightNumber' => 'nullable|string',
            'arrivalTime' => 'nullable|string',
            'hotelNameBali' => 'nullable|string',
            'hotelContactBali' => 'nullable|string',

            // From Lembongan
            'departureDateFromLembongan' => 'nullable|date',
            'departureTimeFromLembongan' => 'nullable|string',
            'dropOffLocationBali' => 'nullable|string',
            'flightTime' => 'nullable|string',
            'hotelNameLembongan' => 'nullable|string',
            'accommodationLembongan' => 'nullable|string',

            // Extra
            'passengerNames' => 'nullable|string',
            'hasSurfboard' => 'required|boolean',
            'hearAboutUs' => 'nullable|string',
            'message' => 'nullable|string',
        ];
    }
}