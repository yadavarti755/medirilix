<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $refund;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct($refund, $status)
    {
        $this->refund = $refund;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Refund Status Update - #' . $this->refund->orderProductList->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.refund-updated', // Simplified view name
            with: [
                'refund' => $this->refund,
                'status' => $this->status,
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
