<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $orderNumber;
    public $orderDetails;
    public $user;

    public function __construct($orderNumber, $orderDetails, $user)
    {
        $this->orderNumber = $orderNumber;
        $this->orderDetails = $orderDetails; // This is OrderProductList
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order #' . $this->orderNumber . ' has been Shipped!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-shipped',
        );
    }
}
