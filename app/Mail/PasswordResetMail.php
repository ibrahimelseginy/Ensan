<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $otpCode;
    public string $resetUrl;

    public function __construct(User $user, string $otpCode, string $resetUrl)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
        $this->resetUrl = $resetUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز التحقق ورابط تعيين كلمة المرور - مؤسسة إنسان',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'user'     => $this->user,
                'otpCode'  => $this->otpCode,
                'resetUrl' => $this->resetUrl,
            ],
        );
    }
}
