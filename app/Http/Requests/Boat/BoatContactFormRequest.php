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

    /**
     * Boat Transfer Booking Form.
     *
     * - ticketType one_way: returnDate / returnTime are ignored
     * - needLandTransport = true: pickUpPoint required: airport | hotel_villa | not_applicable
     *   (flightNumber / arrivalTime are only kept for airport)
     * - bookedThroughTlt = true: tltBookingRefName (Property name / booking info) required
     */
    public function rules()
    {
        return [
            'boatId' => 'nullable|integer|exists:boats,id',

            // Route
            'ticketType' => 'required|in:one_way,return',
            'fromLocation' => 'required|string|max:255',
            'toLocation' => 'required|string|max:255',

            // Departure / return
            'departureDate' => 'required|date',
            'departureTime' => 'required|string|max:50',
            'returnDate' => 'required_if:ticketType,return|nullable|date|after_or_equal:departureDate',
            'returnTime' => 'required_if:ticketType,return|nullable|string|max:50',

            // Passengers
            'adultCount' => 'required|integer|min:1',
            'childCount' => 'required|integer|min:0',
            'infantCount' => 'nullable|integer|min:0',

            // Your details
            'name' => 'required|string|max:255',
            'phoneCountryCode' => 'nullable|string|max:10',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',

            // Land transport
            'needLandTransport' => 'required|boolean',
            'pickUpPoint' => ['required_if:needLandTransport,true,1', 'nullable', Rule::in(array_keys(BoatContactForm::PICK_UP_POINTS))],
            'flightNumber' => 'nullable|string|max:100',
            'arrivalTime' => 'nullable|string|max:50',

            // Extras
            'bookedThroughTlt' => 'required|boolean',
            'tltBookingRefName' => 'required_if:bookedThroughTlt,true,1|nullable|string|max:255',
            'hasSurfboard' => 'required|boolean',
            'message' => 'nullable|string',
        ];
    }

    /**
     * Fields that belong to a closed toggle are not stored.
     */
    protected function passedValidation()
    {
        $clean = [];

        if ($this->ticketType !== 'return') {
            $clean += ['returnDate' => null, 'returnTime' => null];
        }

        if (!$this->boolean('needLandTransport')) {
            $clean += ['pickUpPoint' => null, 'flightNumber' => null, 'arrivalTime' => null];
        }

        if (!$this->boolean('bookedThroughTlt')) {
            $clean += ['tltBookingRefName' => null];
        }

        if ($this->boolean('needLandTransport') && $this->pickUpPoint !== BoatContactForm::PICK_UP_AIRPORT) {
            $clean += ['flightNumber' => null, 'arrivalTime' => null];
        }

        $this->merge($clean + ['infantCount' => $this->infantCount ?? 0]);
    }
}
