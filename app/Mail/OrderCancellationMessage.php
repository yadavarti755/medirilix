<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancellationMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $messageObj;
    public $request;

    public function __construct($messageObj)
    {
        $this->messageObj = $messageObj;
        $this->request = $messageObj->cancellationRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Message Regarding Cancellation Request',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cancellation-message',
        );
    }
}
