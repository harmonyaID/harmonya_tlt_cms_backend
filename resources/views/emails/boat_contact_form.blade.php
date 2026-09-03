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
            <tr><td style="padding: 6px 0; color: #777;">Phone</td><td style="padding: 6px 0;">{{ $contactForm->phone ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Ticket Type</td><td style="padding: 6px 0;">{{ $contactForm->ticketType }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Boat</td><td style="padding: 6px 0;">{{ optional($contactForm->boat)->name ?: '-' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Adults / Children / Infants</td><td style="padding: 6px 0;">{{ $contactForm->adultCount }} / {{ $contactForm->childCount }} / {{ $contactForm->infantCount }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Departure from Bali</td><td style="padding: 6px 0;">{{ $contactForm->departureDateFromBali ?: '-' }} {{ $contactForm->departureTimeFromBali }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Departure from Lembongan</td><td style="padding: 6px 0;">{{ $contactForm->departureDateFromLembongan ?: '-' }} {{ $contactForm->departureTimeFromLembongan }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Booked through TLT</td><td style="padding: 6px 0;">{{ $contactForm->bookedThroughTlt ? 'Yes' : 'No' }}</td></tr>
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