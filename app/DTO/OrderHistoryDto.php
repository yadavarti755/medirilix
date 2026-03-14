<?php

namespace App\DTO;

class OrderHistoryDto
{
    public $user_id;
    public $order_number;
    public $order_status;
    public $status_changed_date;
    public $remarks;
    public $created_by;
    public $updated_by;

    public function __construct(
        $user_id,
        $order_number,
        $order_status,
        $status_changed_date,
        $remarks = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->user_id = $user_id;
        $this->order_number = $order_number;
        $this->order_status = $order_status;
        $this->status_changed_date = $status_changed_date;
        $this->remarks = $remarks;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
