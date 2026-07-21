<?php

namespace App\Mail\WebsiteContactForm;

use App\Models\WebsiteContactForm\WebsiteContactForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public WebsiteContactForm $contactForm;

    public function __construct(WebsiteContactForm $contactForm)
    {
        $this->contactForm = $contactForm;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Website Contact Form - ' . $this->contactForm->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.website_contact_form',
        );
    }
}