<?php

namespace App\DTO;

class OrderCancellationRequestMessageDto
{
    public $order_cancellation_request_id;
    public $message_by;
    public $message;

    public function __construct(
        $order_cancellation_request_id,
        $message_by,
        $message
    ) {
        $this->order_cancellation_request_id = $order_cancellation_request_id;
        $this->message_by = $message_by;
        $this->message = $message;
    }
}
