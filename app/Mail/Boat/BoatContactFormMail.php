<?php

namespace App\Mail\Boat;

use App\Models\Boat\BoatContactForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoatContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public BoatContactForm $contactForm;

    public function __construct(BoatContactForm $contactForm)
    {
        $this->contactForm = $contactForm;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Boat Booking Inquiry - ' . $this->contactForm->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.boat_contact_form',
        );
    }
}