<?php

namespace App\DTO;

class PayuResponseDto
{
    public $user_id;
    public $order_number;
    public $mihpayid;
    public $mode;
    public $status;
    public $unmappedstatus;
    public $key;
    public $txnid;
    public $amount;
    public $cardcategory;
    public $discount;
    public $net_amount_debit;
    public $addedon;
    public $productinfo;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $country;
    public $zipcode;
    public $payment_source;
    public $pg_type;
    public $bank_ref_num;
    public $bankcode;
    public $error;
    public $error_message;
    public $name_on_card;
    public $cardnum;
    public $message;
    public $created_by;
    public $updated_by;

    public function __construct(
        $user_id,
        $order_number,
        $mihpayid,
        $mode,
        $status = null,
        $unmappedstatus = null,
        $key = null,
        $txnid = null,
        $amount = null,
        $cardcategory = null,
        $discount = null,
        $net_amount_debit = null,
        $addedon = null,
        $productinfo = null,
        $firstname = null,
        $lastname = null,
        $email = null,
        $phone = null,
        $address1 = null,
        $address2 = null,
        $city = null,
        $state = null,
        $country = null,
        $zipcode = null,
        $payment_source = null,
        $pg_type = null,
        $bank_ref_num = null,
        $bankcode = null,
        $error = null,
        $error_message = null,
        $name_on_card = null,
        $cardnum = null,
        $message = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->user_id = $user_id;
        $this->order_number = $order_number;
        $this->mihpayid = $mihpayid;
        $this->mode = $mode;
        $this->status = $status;
        $this->unmappedstatus = $unmappedstatus;
        $this->key = $key;
        $this->txnid = $txnid;
        $this->amount = $amount;
        $this->cardcategory = $cardcategory;
        $this->discount = $discount;
        $this->net_amount_debit = $net_amount_debit;
        $this->addedon = $addedon;
        $this->productinfo = $productinfo;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->zipcode = $zipcode;
        $this->payment_source = $payment_source;
        $this->pg_type = $pg_type;
        $this->bank_ref_num = $bank_ref_num;
        $this->bankcode = $bankcode;
        $this->error = $error;
        $this->error_message = $error_message;
        $this->name_on_card = $name_on_card;
        $this->cardnum = $cardnum;
        $this->message = $message;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
