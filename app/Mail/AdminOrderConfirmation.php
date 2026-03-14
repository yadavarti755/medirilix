<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $orderNumber;

    /**
     * Create a new message instance.
     */
    public function __construct($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order Received - ' . $this->orderNumber,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-order-confirmation', // Placeholder view
            with: [
                'orderNumber' => $this->orderNumber,
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
