<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\DTO\ContactUsDto;

class ContactQueryAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contactUsDto;

    /**
     * Create a new message instance.
     */
    public function __construct($contactUsDto)
    {
        $this->contactUsDto = $contactUsDto;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Us Query',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.contact-query',
            with: [
                'data' => $this->contactUsDto,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
