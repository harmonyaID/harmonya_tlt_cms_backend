<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name = "";

    public string $email = "";

    public string $password = "";

    public function __construct($account, $password)
    {
        $this->name = $account->fullName;
        $this->email = $account->user?->email;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TLT Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password',
        );
    }
}
