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
            @if($inquiryForm->propertyVariant)
                <tr><td style="padding: 6px 0; color: #777;">Unit</td><td style="padding: 6px 0;">{{ $inquiryForm->propertyVariant->nickname }}</td></tr>
            @endif
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

        @if($quote = $inquiryForm->quote())
            @php($money = fn ($amount, $currency = null) => $amount === null ? '-' : trim(($currency ?? $quote['currency']) . ' ' . number_format((float) $amount, 2)))
            <p style="margin-top: 16px; color: #777;">Price shown to the guest</p>
            <table style="width: 100%; border-collapse: collapse;">
                <tr><td style="padding: 6px 0; width: 200px; color: #777;">Price / Night</td><td style="padding: 6px 0;">{{ $money($quote['pricePerNight']) }}</td></tr>
                <tr><td style="padding: 6px 0; color: #777;">Nights</td><td style="padding: 6px 0;">{{ $quote['nights'] ?? '-' }}</td></tr>
                <tr><td style="padding: 6px 0; color: #777;">Fee</td><td style="padding: 6px 0;">{{ $money($quote['fee']) }}</td></tr>
                <tr><td style="padding: 6px 0; color: #777;">Total</td><td style="padding: 6px 0;"><b>{{ $money($quote['total']) }}</b></td></tr>
                @if($quote['payableAmount'] !== null)
                    <tr><td style="padding: 6px 0; color: #777;">Guest Pays (estimate)</td><td style="padding: 6px 0;">{{ $money($quote['payableAmount'], $quote['payableCurrency']) }}</td></tr>
                @endif
                @if($quote['rateOptions'])
                    <tr><td style="padding: 6px 0; color: #777;">Rate Options</td><td style="padding: 6px 0;">{{ implode(', ', $quote['rateOptions']) }}</td></tr>
                @endif
                @if($quote['couponCode'])
                    <tr><td style="padding: 6px 0; color: #777;">Coupon</td><td style="padding: 6px 0;">{{ $quote['couponCode'] }}</td></tr>
                @endif
            </table>
        @endif

        @if($inquiryForm->comments)
            <p style="margin-top: 16px; color: #777;">Comments</p>
            <p style="background: #f5f5f5; padding: 12px; border-radius: 4px;">{{ $inquiryForm->comments }}</p>
        @endif

        <p style="margin-top: 24px; color: #aaa; font-size: 12px;">This email was sent automatically from the TLT admin system.</p>
    </div>
</body>
</html>
