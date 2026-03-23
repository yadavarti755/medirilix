<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResendVerificationCodeMail extends Mailable
{
    use SerializesModels;

    public $user;
    public $code;
    public $expiryMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $code, $expiryMinutes = 15)
    {
        $this->user = $user;
        $this->code = $code;
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Verification Code - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.resend-verification-code',
            with: [
                'userName' => $this->user->name,
                'verificationCode' => $this->code,
                'expiryMinutes' => $this->expiryMinutes,
                'verifyUrl' => route('verify-user'),
                'supportEmail' => config('mail.from.address'),
                'appName' => config('app.name'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
