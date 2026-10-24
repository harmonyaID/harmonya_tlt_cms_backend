<?php

namespace App\Http\Requests\Boat;

use App\Models\Boat\BoatContactForm;
use Illuminate\Validation\Rule;
use Logia\Core\Validation\Support\FormRequest;

class BoatContactFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $points = Rule::in(array_keys(BoatContactForm::PICK_UP_POINTS));

        return [
            'boatId' => 'nullable|integer|exists:boats,id',

            'ticketType' => 'required|in:one_way,return',
            'fromLocation' => 'required|string|max:255',
            'toLocation' => 'required|string|max:255',

            'departureDate' => 'required|date',
            'departureTime' => 'required|string|max:50',
            'returnDate' => 'required_if:ticketType,return|nullable|date|after_or_equal:departureDate',
            'returnTime' => 'required_if:ticketType,return|nullable|string|max:50',

            'adultCount' => 'required|integer|min:1',
            'childCount' => 'required|integer|min:0',
            'infantCount' => 'nullable|integer|min:0',

            'name' => 'required|string|max:255',
            'phoneCountryCode' => 'nullable|string|max:10',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',

            'needLandTransport' => 'required|boolean',
            'departurePickupPoint' => ['required_if:needLandTransport,true,1', 'nullable', $points],
            'departurePickupFlightNumber' => 'nullable|string|max:100',
            'departurePickupArrivalTime' => 'nullable|string|max:50',
            'departureDropOffPoint' => ['nullable', $points],
            'departureDropOffLocation' => 'nullable|string|max:255',
            'departureDropOffArrivalTime' => 'nullable|string|max:50',
            // return leg (only for ticketType = return)
            'returnPickupPoint' => ['nullable', $points],
            'returnPickupLocation' => 'nullable|string|max:255',
            'returnPickupCheckoutTime' => 'nullable|string|max:50',
            'returnDropOffPoint' => ['nullable', $points],
            'returnDropOffLocation' => 'nullable|string|max:255',

            // Extras
            'bookedThroughTlt' => 'required|boolean',
            'tltBookingRefName' => 'required_if:bookedThroughTlt,true,1|nullable|string|max:255',
            'hasSurfboard' => 'required|boolean',
            'message' => 'nullable|string',
        ];
    }

    protected function passedValidation()
    {
        $clean = [];

        if ($this->ticketType !== 'return') {
            $clean += ['returnDate' => null, 'returnTime' => null];
        }

        if (!$this->boolean('needLandTransport') || $this->ticketType !== 'return') {
            $returnLeg = array_fill_keys([
                'returnPickupPoint', 'returnPickupLocation', 'returnPickupCheckoutTime',
                'returnDropOffPoint', 'returnDropOffLocation',
            ], null);
            $clean += $returnLeg;
        }

        if (!$this->boolean('needLandTransport')) {
            $clean += array_fill_keys([
                'departurePickupPoint', 'departurePickupFlightNumber', 'departurePickupArrivalTime',
                'departureDropOffPoint', 'departureDropOffLocation', 'departureDropOffArrivalTime',
            ], null);
        }

        if (!$this->boolean('bookedThroughTlt')) {
            $clean += ['tltBookingRefName' => null];
        }

        if ($this->boolean('needLandTransport') && $this->departurePickupPoint !== BoatContactForm::PICK_UP_AIRPORT) {
            $clean += ['departurePickupFlightNumber' => null, 'departurePickupArrivalTime' => null];
        }

        $this->merge($clean + ['infantCount' => $this->infantCount ?? 0]);
    }
}
