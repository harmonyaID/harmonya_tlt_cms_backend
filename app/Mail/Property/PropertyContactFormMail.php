<?php

namespace App\Mail\Property;

use App\Models\Property\PropertyContactForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public PropertyContactForm $contactForm;

    public function __construct(PropertyContactForm $contactForm)
    {
        $this->contactForm = $contactForm;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Property Booking Inquiry - ' . $this->contactForm->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.property_contact_form',
        );
    }
}
