<?php

namespace App\DTO;

class OrderProductListDto
{
    public $user_id;
    public $order_number;
    public $product_id;
    public $product_featured_image;
    public $product_name;
    public $size;
    public $material;
    public $price;
    public $quantity;
    public $total_price;
    public $product_order_status;
    public $status_changed_date;
    public $status_changed_by;
    public $remarks;
    public $cancel_reason;
    public $discount_amount;
    public $tax_amount;
    public $created_by;
    public $updated_by;

    public function __construct(
        $user_id,
        $order_number,
        $product_id,
        $product_featured_image = 'no-image.png',
        $product_name = null,
        $size = null,
        $material = null,
        $price = null,
        $quantity = null,
        $total_price = null,
        $product_order_status = null,
        $status_changed_date = null,
        $status_changed_by = null,
        $remarks = null,
        $cancel_reason = null,
        $discount_amount = 0,
        $tax_amount = 0,
        $created_by = null,
        $updated_by = null
    ) {
        $this->user_id = $user_id;
        $this->order_number = $order_number;
        $this->product_id = $product_id;
        $this->product_featured_image = $product_featured_image;
        $this->product_name = $product_name;
        $this->size = $size;
        $this->material = $material;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->total_price = $total_price;
        $this->product_order_status = $product_order_status;
        $this->status_changed_date = $status_changed_date;
        $this->status_changed_by = $status_changed_by;
        $this->remarks = $remarks;
        $this->cancel_reason = $cancel_reason;
        $this->discount_amount = $discount_amount;
        $this->tax_amount = $tax_amount;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
