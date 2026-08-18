<?php

namespace App\Mail\Property;

use App\Models\Property\PropertyInquiryForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyInquiryFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public PropertyInquiryForm $inquiryForm;

    public function __construct(PropertyInquiryForm $inquiryForm)
    {
        $this->inquiryForm = $inquiryForm;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Property Inquiry - ' . $this->inquiryForm->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.property_inquiry_form',
        );
    }
}
