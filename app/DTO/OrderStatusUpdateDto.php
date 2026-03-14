<?php

namespace App\DTO;

class OrderStatusUpdateDto
{
    public $order_number;
    public $order_status;
    public $remarks;
    public $updated_by;

    public function __construct(
        $order_number,
        $order_status,
        $remarks,
        $updated_by
    ) {
        $this->order_number = $order_number;
        $this->order_status = $order_status;
        $this->remarks = $remarks;
        $this->updated_by = $updated_by;
    }
}
