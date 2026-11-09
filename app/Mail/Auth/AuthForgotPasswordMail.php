<?php

namespace App\Mail\Auth;

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

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TLT Forgot Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.forgot_password',
        );
    }
}
