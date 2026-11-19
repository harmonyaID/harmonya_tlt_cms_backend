<?php

namespace App\Mail\Experience;

use App\Models\Experience\ExperienceInquiryForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExperienceInquiryFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public ExperienceInquiryForm $form;

    public function __construct(ExperienceInquiryForm $form)
    {
        $this->form = $form;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Experience Inquiry - ' . $this->form->fullName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.experience_inquiry_form',
        );
    }
}