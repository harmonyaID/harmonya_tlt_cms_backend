<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Property Inquiry</title>
</head>
<body style="font-family: Helvetica, Arial, sans-serif; color: #333; font-size: 14px;">
    <div style="max-width: 600px; margin: 0 auto; padding: 24px;">
        <h2 style="margin-bottom: 4px;">New Property Inquiry</h2>
        <p style="color: #777; margin-top: 0;">Submitted via website property inquiry form</p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
            <tr><td style="padding: 6px 0; width: 200px; color: #777;">Name</td><td style="padding: 6px 0;"><b>{{ $inquiryForm->name }}</b></td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Email</td><td style="padding: 6px 0;">{{ $inquiryForm->email }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Mobile Number</td><td style="padding: 6px 0;">{{ $inquiryForm->countryCode }} {{ $inquiryForm->mobileNumber }}</td></tr>
            <tr><td style="padding: 6px 0; color: #777;">Property</td><td style="padding: 6px 0;">{{ optional($inquiryForm->property)->nickname ?: '-' }}</td></tr>
            @if($inquiryForm->isDatesFlexible)
                <tr><td style="padding: 6px 0; color: #777;">Dates</td><td style="padding: 6px 0;">Flexible &ndash; {{ $inquiryForm->flexibleMonth }} {{ $inquiryForm->flexibleYear }}</td></tr>
            @else
                <tr><td style="padding: 6px 0; color: #777;">Check-in / Check-out</td><td style="padding: 6px 0;">{{ optional($inquiryForm->checkInDate)->format('d/m/Y') ?: '-' }} &ndash; {{ optional($inquiryForm->checkOutDate)->format('d/m/Y') ?: '-' }}</td></tr>
            @endif
            <tr><td style="padding: 6px 0; color: #777;">Adults</td><td style="padding: 6px 0;">{{ $inquiryForm->adultCount }}</td></tr>
            <tr>
                <td style="padding: 6px 0; color: #777;">Children</td>
                <td style="padding: 6px 0;">
                    @if($inquiryForm->childrenAges && count($inquiryForm->childrenAges) > 0)
                        {{ count($inquiryForm->childrenAges) }} (ages: {{ implode(', ', $inquiryForm->childrenAges) }})
                    @else
                        0
                    @endif
                </td>
            </tr>
        </table>

        @if($inquiryForm->comments)
            <p style="margin-top: 16px; color: #777;">Comments</p>
            <p style="background: #f5f5f5; padding: 12px; border-radius: 4px;">{{ $inquiryForm->comments }}</p>
        @endif

        <p style="margin-top: 24px; color: #aaa; font-size: 12px;">This email was sent automatically from the TLT admin system.</p>
    </div>
</body>
</html>
