<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Boat Booking Inquiry</title>
</head>
<body style="font-family: Helvetica, Arial, sans-serif; color: #333; font-size: 14px;">
    <div style="max-width: 600px; margin: 0 auto; padding: 24px;">
        <h2 style="margin-bottom: 4px;">New Boat Booking Inquiry</h2>
        <p style="color: #777; margin-top: 0;">Submitted via website boat contact form</p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
            <tr><td style="padding: 6px 0; width: 200px; color: #777;">Name</td><td style="padding: 6px 0;"><b>{{ $contactForm->name }}</b></td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Email</td><td style="padding: 6px 0;">{{ $contactForm->email }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Phone</td><td style="padding: 6px 0;">{{ trim($contactForm->phoneCountryCode . ' ' . $contactForm->phone) ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Ticket Type</td><td style="padding: 6px 0;">{{ $contactForm->ticketType }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Boat</td><td style="padding: 6px 0;">{{ optional($contactForm->boat)->name ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Adults / Children / Infants</td><td style="padding: 6px 0;">{{ $contactForm->adultCount }} / {{ $contactForm->childCount }} / {{ $contactForm->infantCount }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Route</td><td style="padding: 6px 0;">{{ $contactForm->fromLocation ?: '-' }} &rarr; {{ $contactForm->toLocation ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Departure</td><td style="padding: 6px 0;">{{ optional($contactForm->departureDate)->format('d/m/Y') ?: '-' }} {{ $contactForm->departureTime }}</td></tr>
            @if($contactForm->ticketType === 'return')
            <tr><td style="padding: 6px 0; color: #777;">Return</td><td style="padding: 6px 0;">{{ optional($contactForm->returnDate)->format('d/m/Y') ?: '-' }} {{ $contactForm->returnTime }}</td></tr>
            @endif
            @if($contactForm->needLandTransport)
            <tr><td style="padding: 6px 0; color: #777;">Departure - pick up</td><td style="padding: 6px 0;">{{ \App\Models\Boat\BoatContactForm::pointLabel($contactForm->departurePickupPoint) ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Departure - flight number / arrival time</td><td style="padding: 6px 0;">{{ $contactForm->departurePickupFlightNumber ?: '-' }} / {{ $contactForm->departurePickupArrivalTime ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Departure - drop off</td><td style="padding: 6px 0;">{{ \App\Models\Boat\BoatContactForm::pointLabel($contactForm->departureDropOffPoint) ?: '-' }} &middot; {{ $contactForm->departureDropOffLocation ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Departure - drop off arrival time</td><td style="padding: 6px 0;">{{ $contactForm->departureDropOffArrivalTime ?: '-' }}</td></tr>
            @if($contactForm->ticketType === 'return')
            <tr><td style="padding: 6px 0; color: #777;">Return - pick up</td><td style="padding: 6px 0;">{{ \App\Models\Boat\BoatContactForm::pointLabel($contactForm->returnPickupPoint) ?: '-' }} &middot; {{ $contactForm->returnPickupLocation ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Return - checkout time</td><td style="padding: 6px 0;">{{ $contactForm->returnPickupCheckoutTime ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Return - drop off</td><td style="padding: 6px 0;">{{ \App\Models\Boat\BoatContactForm::pointLabel($contactForm->returnDropOffPoint) ?: '-' }} &middot; {{ $contactForm->returnDropOffLocation ?: '-' }}</td></tr>
            @endif
            @else
            <tr><td style="padding: 6px 0; color: #777;">Land transport</td><td style="padding: 6px 0;">No</td></tr>
            @endif
            <tr><td style="padding: 6px 0; color: #777;">Booked through TLT</td><td style="padding: 6px 0;">{{ $contactForm->bookedThroughTlt ? 'Yes' : 'No' }}</td></tr>
            @if($contactForm->bookedThroughTlt)
            <tr><td style="padding: 6px 0; color: #777;">Property name / Booking info</td><td style="padding: 6px 0;">{{ $contactForm->tltBookingRefName ?: '-' }}</td></tr>
            @endif
            <tr><td style="padding: 6px 0; color: #777;">Has Surfboard</td><td style="padding: 6px 0;">{{ $contactForm->hasSurfboard ? 'Yes' : 'No' }}</td></tr>
        </table>

        @if($contactForm->message)
            <p style="margin-top: 16px; color: #777;">Message</p>
            <p style="background: #f5f5f5; padding: 12px; border-radius: 4px;">{{ $contactForm->message }}</p>
        @endif

        <p style="margin-top: 24px; color: #aaa; font-size: 12px;">This email was sent automatically from the TLT admin system.</p>
    </div>
</body>
</html>