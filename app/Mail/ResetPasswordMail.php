<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $token
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yêu cầu đặt lại mật khẩu - eNews AGU',
        );
    }

    public function content(): Content
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->email,
        ]);

        return new Content(
            view: 'emails.reset-password',
            with: [
                'user'     => $this->user,
                'resetUrl' => $resetUrl,
            ],
        );
    }
}
