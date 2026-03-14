<?php

namespace App\DTO;

class OrderDto
{
    public $order_number;
    public $user_id;
    public $order_status;
    public $order_date;
    public $total_price;
    public $tax_price;
    public $shipping_price;
    public $grand_total;
    public $payment_method;
    public $payment_status;
    public $cancel_reason;
    public $order_status_changed_date;
    public $updated_by;
    public $payment_type;
    public $subtotal_price;
    public $created_by;
    public $discount_amount;
    public $coupon_code;
    public $coupon_data;
    public $tax_amount;
    public $shipping_charges;
    // Add other fields as necessary

    public function __construct(
        $order_number = null,
        $user_id = null,
        $order_status = null,
        $order_date = null,
        $total_price = null,
        $tax_price = null,
        $shipping_price = null,
        $grand_total = null,
        $payment_method = null,
        $payment_status = null,
        $cancel_reason = null,
        $order_status_changed_date = null,
        $payment_type = null,
        $subtotal_price = null,
        $created_by = null,
        $updated_by = null,
        $discount_amount = 0,
        $coupon_code = null,
        $coupon_data = null,
        $tax_amount = 0,
        $shipping_charges = 0
    ) {
        $this->order_number = $order_number;
        $this->user_id = $user_id;
        $this->order_status = $order_status;
        $this->order_date = $order_date;
        $this->total_price = $total_price;
        $this->tax_price = $tax_price;
        $this->shipping_price = $shipping_price;
        $this->grand_total = $grand_total;
        $this->payment_method = $payment_method;
        $this->payment_status = $payment_status;
        $this->cancel_reason = $cancel_reason;
        $this->order_status_changed_date = $order_status_changed_date;
        $this->payment_type = $payment_type;
        $this->subtotal_price = $subtotal_price;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->discount_amount = $discount_amount;
        $this->coupon_code = $coupon_code;
        $this->coupon_data = $coupon_data;
        $this->tax_amount = $tax_amount;
        $this->shipping_charges = $shipping_charges;
    }
}
