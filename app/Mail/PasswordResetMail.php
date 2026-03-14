<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\SiteSetting;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $email;
    public $name;
    public $isNewUser;


    /**
     * Create a new message instance.
     */
    public function __construct($resetUrl, $email, $name = '', $isNewUser = false)
    {
        $this->resetUrl = $resetUrl;
        $this->email = $email;
        $this->name = $name;
        $this->isNewUser = $isNewUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = SiteSetting::first()?->site_name ?? config('app.name');
        $subject = $this->isNewUser ? 'Set Your ' . $siteName . ' Account Password' : 'Reset Your ' . $siteName . ' Account Password';
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.password_reset',
            with: [
                'resetUrl' => $this->resetUrl,
                'email' => $this->email,
                'isNewUser' => $this->isNewUser,
                'name' => $this->name,
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
