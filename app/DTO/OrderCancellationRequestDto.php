<?php

namespace App\DTO;

class OrderCancellationRequestDto
{
    public $order_product_list_id;
    public $user_id;
    public $cancel_reason_id;
    public $description;
    public $status;
    public $status_changed_by;
    public $created_by;
    public $updated_by;

    public function __construct(
        $order_product_list_id,
        $user_id,
        $cancel_reason_id,
        $description,
        $status = 'Pending',
        $status_changed_by = null,
        $created_by,
        $updated_by
    ) {
        $this->order_product_list_id = $order_product_list_id;
        $this->user_id = $user_id;
        $this->cancel_reason_id = $cancel_reason_id;
        $this->description = $description;
        $this->status = $status;
        $this->status_changed_by = $status_changed_by;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
