<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancellationClosed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;
    public $productName;
    public $orderNumber;

    public function __construct($request)
    {
        $this->request = $request;
        $this->productName = $request->orderProductList->product_name ?? 'Item';
        $this->orderNumber = $request->orderProductList->order->order_number ?? 'N/A';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cancellation Request Closed - Order #' . $this->orderNumber,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cancellation-closed',
        );
    }
}
