<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReturn extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $orderNumber;
    public $order;

    public function __construct($orderNumber, $order)
    {
        $this->orderNumber = $orderNumber;
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Return Request Received - Order #' . $this->orderNumber,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-return',
        );
    }
}
