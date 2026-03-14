<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DHTICertificateIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $applicant;
    public string $courseCode;
    public string $encodedUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(array $applicant, string $courseCode, $encodedUrl)
    {
        $this->applicant = $applicant;
        $this->courseCode = $courseCode;
        $this->encodedUrl = $encodedUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Certificate Issued - Application #' . $this->applicant['application_number'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.dhti.certificate_issued',
            with: [
                'applicant' => $this->applicant,
                'courseCode' => $this->courseCode,
                'encodedUrl' => $this->encodedUrl,
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
