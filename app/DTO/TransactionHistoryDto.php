<?php

namespace App\DTO;

class TransactionHistoryDto
{
    public $user_id;
    public $txn_id;
    public $order_number;
    public $amount;
    public $transaction_date;
    public $payment_status;
    public $message;
    public $remarks;
    public $created_by;
    public $updated_by;

    public function __construct(
        $user_id,
        $txn_id,
        $order_number,
        $amount,
        $transaction_date,
        $payment_status,
        $message = null,
        $remarks = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->user_id = $user_id;
        $this->txn_id = $txn_id;
        $this->order_number = $order_number;
        $this->amount = $amount;
        $this->transaction_date = $transaction_date;
        $this->payment_status = $payment_status;
        $this->message = $message;
        $this->remarks = $remarks;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
