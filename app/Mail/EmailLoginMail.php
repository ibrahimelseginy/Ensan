<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailLoginMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $otpCode,
        public string $magicUrl,
        public int $expiresMinutes = 10
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔐 كود تسجيل الدخول لمنظومة إنسان ({$this->otpCode})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email-login',
        );
    }
}

