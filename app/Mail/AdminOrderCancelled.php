<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderCancelled extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $orderNumber;
    public $orderDetails;

    public function __construct($orderNumber, $orderDetails)
    {
        $this->orderNumber = $orderNumber;
        $this->orderDetails = $orderDetails;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Order #' . $this->orderNumber,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-order-cancelled',
        );
    }
}
