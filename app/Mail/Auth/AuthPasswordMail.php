<?php

namespace App\Mail\Auth;

use App\Models\Member\Member;
use App\Models\Partner\Partner;
use App\Models\Staff\Staff;
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

    /**
     * Create a new message instance.
     */
    public function __construct($account, $password)
    {
        $this->name = $account->fullName;
        $this->email = $account->user?->email;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TLT Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password',
        );
    }

}
