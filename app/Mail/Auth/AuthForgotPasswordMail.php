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

class AuthForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name = "";
    public string $resetLink = "";

    /**
     * Create a new message instance.
     */
    public function __construct($account, $userPassword)
    {
        $this->name = $account->fullName ?: $account->name;

        switch ($account::class) {
            case Staff::class:
                $this->resetLink = config('auth.forgot-password-link.staff');
                break;
        }

        $this->resetLink .= "?token=" . $userPassword->token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TLT Forgot Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.forgot_password',
        );
    }

}
