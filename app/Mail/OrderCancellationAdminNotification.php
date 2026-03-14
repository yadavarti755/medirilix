<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\OrderCancellationRequest; // created alias if needed, or use full path

class OrderCancellationAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $cancellationRequest;

    /**
     * Create a new message instance.
     */
    public function __construct($cancellationRequest)
    {
        $this->cancellationRequest = $cancellationRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Cancellation Request',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.cancellation-request',
            with: [
                'cancellationRequest' => $this->cancellationRequest,
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
