<?php

namespace App\DTO;

class OrderProductShippingDetailDto
{
    public function __construct(
        public $order_product_list_id,
        public $order_status,
        public $shipment_photos,
        public $shipping_details,
        public $dhl_tracking_id,
        public $created_by,
        public $updated_by
    ) {}
}
