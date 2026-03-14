<?php

namespace App\DTO;

class ReturnRequestDto
{
    public $user_id;
    public $order_product_list_id;
    public $return_list_id;
    public $return_description;
    public $return_status;
    public $return_pickup_details;
    public $created_by;
    public $updated_by;

    public function __construct(
        $user_id,
        $order_product_list_id,
        $return_list_id,
        $return_description,
        $return_status = 'RETURN_REQUESTED',
        $return_pickup_details = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->user_id = $user_id;
        $this->order_product_list_id = $order_product_list_id;
        $this->return_list_id = $return_list_id;
        $this->return_description = $return_description;
        $this->return_status = $return_status;
        $this->return_pickup_details = $return_pickup_details;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }

    public function toArray()
    {
        return [
            'user_id' => $this->user_id,
            'order_product_list_id' => $this->order_product_list_id,
            'return_list_id' => $this->return_list_id,
            'return_description' => $this->return_description,
            'return_status' => $this->return_status,
            'return_pickup_details' => $this->return_pickup_details,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
