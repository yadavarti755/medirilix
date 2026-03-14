<?php

namespace App\DTO;

class RefundDto
{
    public $order_product_list_id;
    public $refund_amount;
    public $refund_status;
    public $remarks;
    public $created_by;
    public $updated_by;

    public function __construct(
        $order_product_list_id,
        $refund_amount,
        $refund_status,
        $remarks,
        $created_by,
        $updated_by
    ) {
        $this->order_product_list_id = $order_product_list_id;
        $this->refund_amount = $refund_amount;
        $this->refund_status = $refund_status;
        $this->remarks = $remarks;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
